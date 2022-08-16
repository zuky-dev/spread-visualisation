<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use App\Traits\FilesTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    use FilesTrait;

    protected BaseRepository $repository;
    protected $relations = [];
    protected $pivots = [];
    protected $files = [];

    public function all() {
        return $this->repository
            ->order('id')
            ->get();
    }

    public function paginate(array $request = []) {
        $repo = $this->repository;
        $perPage = null;
        $page = null;

        if (!isset($request['order']) || is_null($request['order'])) {
            $request['order'] = 'desc';
        }

        foreach ($request as $key => $value) {
            if (!is_null($value)) {
                switch ($key) {
                    case 'order':
                        $repo = $repo->order('created_at', $value);
                        break;
                    case 'pageSize':
                        $perPage = $value;
                        break;
                    case 'pageIndex':
                        $page = $value;
                        break;
                    default:
                        break;
                }
            }
        }

        return $repo->page($page)->paginate(!is_null($perPage) ? $perPage : 20);
    }

    public function create(array $data) {
        $data = $this->convertRelations($data);
        $dataChanged = $this->convertVariables($data);
        $dataChanged = $this->uploadFiles($dataChanged);

        $model = $this->repository
            ->createModel($dataChanged);

        $this->syncPivots($model, $data);

        return $model
            ->load(array_merge($this->pivots, $this->relations));
    }

    public function read(int $id) {
        return $this->repository
            ->findById($id);
    }

    public function update(int $id, array $data) {
        $data = $this->convertRelations($data);
        $dataChanged = $this->convertVariables($data);

        $model = $this->repository->findById($id);
        $dataChanged = $this->uploadFiles($dataChanged);

        $this->repository->updateModel($model, $dataChanged);

        $this->syncPivots($model, $data);

        return $model
            ->load(array_merge($this->pivots, $this->relations));
    }

    public function delete(int $id) {
        $model = $this->repository->findById($id);

        return $this->repository
            ->deleteModel($model);
    }

    protected function filePath(array $data = null, Model $model = null): string
    {
        $modelRepo = $this->repository->getModel();
        return strtolower(class_basename($modelRepo));
    }

    protected function fileName(array $data = null, Model $model = null): string
    {
        return now()->format('d-m-Y') . '-file-' . rand(11111, 99999);
    }

    protected function uploadFiles(array $data, Model $model = null): array
    {

        foreach ($this->files as $key => $value) {
            if (isset($data[$key]) && !is_null($data[$key])) {

                if (!is_null($model) && !is_null($model->{$value})) {
                    // $this->deleteFileOrDir($model->{$value});
                }

                $file = $data[$key];
                $ext = $file->getClientOriginalExtension();

                $path = $this->filePath($data, $model);
                $name = $this->fileName($data, $model);
                $this->saveFile($file, $path, $name);

                $data[$value] = $path . '/' . $name . '.' . $ext;
            }
            unset($data[$key]);
        }

        return $data;
    }

    protected function convertRelations(array $data): array
    {
        $key = 'customer';
        foreach ($this->relations as $key) {
            if (isset($data[$key]) && !is_null($data[$key]) && is_array($data[$key]) && isset($data[$key]['id'])) {
                $data[$key . 'Id'] = $data[$key]['id'];
            }
            unset($data[$key]);
        }

        return $data;
    }

    protected function convertVariables(array $data): array
    {
        $dataChanged = [];
        foreach ($data as $key => $value) {
            $dataChanged[Str::snake($key)] = $value;
        }

        return $dataChanged;
    }

    protected function syncPivots(Model $model, array $data): void
    {
        foreach ($this->pivots as $key) {
            if (isset($data[$key]) && !is_null($data[$key])) {
                $temporary = [];
                foreach ($data[$key] as $object) {
                    if (isset($object['id']) && !is_null($object['id'])) {
                        $id = $object['id'];
                        unset($object['id']);
                        unset($object['name']);

                        $temporary[$id] = $this->convertVariables([]);
                    }
                }

                $model->{$key}()->sync($temporary);
            }
        }
    }

}

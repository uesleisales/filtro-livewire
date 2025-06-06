<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractCrudController extends Controller
{
    abstract protected function getService();
    abstract protected function getViewPrefix(): string;
    abstract protected function getRoutePrefix(): string;
    abstract protected function getEntityName(): string;
    abstract protected function getEntityNamePlural(): string;
    abstract protected function getExceptionClass(): string;
    abstract protected function getWithProductsMethod(): string;

    public function index(): View
    {
        try {
            $entities = $this->getService()->getAllPaginated();
            $viewData = [$this->getEntityNamePlural() => $entities];
            return view($this->getViewPrefix() . '.index', $viewData);
        } catch (\Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            if ($e instanceof $exceptionClass) {
                Log::error('Error in ' . static::class . '@index', ['error' => $e->getMessage()]);
                $viewData = [$this->getEntityNamePlural() => collect()];
                return view($this->getViewPrefix() . '.index', $viewData)
                    ->withErrors(['error' => 'Erro ao carregar ' . $this->getEntityNamePlural() . '.']);
            }
            throw $e;
        }
    }

    public function create(): View
    {
        return view($this->getViewPrefix() . '.create');
    }

    public function store(FormRequest $request): RedirectResponse
    {
        try {
            $this->getService()->create($request->validated());
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', ucfirst($this->getEntityName()) . ' criada com sucesso!');
        } catch (\Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            if ($e instanceof $exceptionClass) {
                return redirect()->back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
            throw $e;
        }
    }

    public function show(int $id): View
    {
        try {
            $entity = $this->getService()->{$this->getWithProductsMethod()}($id);
            $viewData = [$this->getEntityName() => $entity];
            return view($this->getViewPrefix() . '.show', $viewData);
        } catch (\Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            if ($e instanceof $exceptionClass) {
                $viewData = [$this->getEntityName() => null];
                return view($this->getViewPrefix() . '.show', $viewData)
                    ->withErrors(['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }

    public function edit(int $id): View
    {
        try {
            $entity = $this->getService()->findById($id);
            $viewData = [$this->getEntityName() => $entity];
            return view($this->getViewPrefix() . '.edit', $viewData);
        } catch (\Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            if ($e instanceof $exceptionClass) {
                $viewData = [$this->getEntityName() => null];
                return view($this->getViewPrefix() . '.edit', $viewData)
                    ->withErrors(['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }

    public function update(FormRequest $request, int $id): RedirectResponse
    {
        try {
            $this->getService()->update($id, $request->validated());
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', ucfirst($this->getEntityName()) . ' atualizada com sucesso!');
        } catch (\Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            if ($e instanceof $exceptionClass) {
                return redirect()->back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
            throw $e;
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->getService()->delete($id);
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', ucfirst($this->getEntityName()) . ' excluída com sucesso!');
        } catch (\Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            if ($e instanceof $exceptionClass) {
                return redirect()->route($this->getRoutePrefix() . '.index')
                    ->withErrors(['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }
}
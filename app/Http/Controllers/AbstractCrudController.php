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

    public function show($entity): View
    {
        $viewData = [$this->getEntityName() => $entity];
        return view($this->getViewPrefix() . '.show', $viewData);
    }

    public function edit($entity): View
    {
        $viewData = [$this->getEntityName() => $entity];
        return view($this->getViewPrefix() . '.edit', $viewData);
    }

    public function update(FormRequest $request, $entity): RedirectResponse
    {
        try {
            $data = $request->validated();
            $this->getService()->update($entity->id, $data);
            
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', ucfirst($this->getEntityName()) . ' atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($entity): RedirectResponse
    {
        try {
            $this->getService()->delete($entity->id);
            
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', ucfirst($this->getEntityName()) . ' excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
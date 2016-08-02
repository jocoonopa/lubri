<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TestEntityCreateRequest;
use App\Http\Requests\TestEntityUpdateRequest;
use App\Repositories\TestEntityRepository;
use App\Validators\TestEntityValidator;


class TestEntitiesController extends Controller
{

    /**
     * @var TestEntityRepository
     */
    protected $repository;

    /**
     * @var TestEntityValidator
     */
    protected $validator;

    public function __construct(TestEntityRepository $repository, TestEntityValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $testEntities = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $testEntities,
            ]);
        }

        return view('testEntities.index', compact('testEntities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TestEntityCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TestEntityCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $testEntity = $this->repository->create($request->all());

            $response = [
                'message' => 'TestEntity created.',
                'data'    => $testEntity->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $testEntity = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $testEntity,
            ]);
        }

        return view('testEntities.show', compact('testEntity'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $testEntity = $this->repository->find($id);

        return view('testEntities.edit', compact('testEntity'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  TestEntityUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(TestEntityUpdateRequest $request, $id)
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $testEntity = $this->repository->update($id, $request->all());

            $response = [
                'message' => 'TestEntity updated.',
                'data'    => $testEntity->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'TestEntity deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'TestEntity deleted.');
    }
}

<?php

namespace App\Lib\Http;

interface RestController
{
    /**
     * Must return a list of the requested entity
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request);

    /**
     * Must show the single requested entity
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request);

    /**
     * Must store a new entity resource
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request);

    /**
     * Must update an already stored resource
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request);

    /**
     * Must delete the resource record
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request);
}
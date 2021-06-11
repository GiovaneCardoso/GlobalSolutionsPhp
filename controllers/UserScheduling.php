<?php

class UserScheduling{

    protected $model;

    public function __construct()
    {
        $this->model = new app\models\UserScheduling();
    }

    /**
     * @param $items
     * @return mixed
     */
    private function addRelations($items )
    {
        $user = new \app\models\UserVehicle();
        $dealership = new \app\models\Dealership();

        foreach ($items as $item) {
            $item->user = $user->find($item->user_id);
            $item->dealership = $dealership->find($item->dealership_id);
        }

        return $items;
    }

    /**
     * @param array $request
     * @return mixed
     * @throws Exception
     */
    public function create( array $request )
    {
        return responseJson( $this->model->create($request) );
    }

    /**
     * @param array|null $request
     */
    public function index( array $request = null )
    {
        responseJson( $this->addRelations( $this->model->selectBy([ 'user_id' => $request['user_id']]) ) );
    }

    /**
     * @param array $request
     * @return mixed
     * @throws Exception
     */
    public function update( array $request )
    {
        $id = $request['id'];

        unset($request['id']);

        return responseJson($this->model->update($id, $request));
    }

    /**
     * @param array $request
     * @return mixed
     * @throws Exception
     */
    public function delete( array $request )
    {
        $id = $request['id'];

        return responseJson($this->model->remove($id));
    }
}
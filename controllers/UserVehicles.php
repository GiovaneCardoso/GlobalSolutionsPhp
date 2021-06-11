<?php


class UserVehicles
{
    public $model;

    /**
     * Dealerships constructor.
     */
    public function __construct()
    {
        $this->model = new \app\models\UserVehicle();
    }

    /**
     * @param $items
     * @return mixed
     */
    private function addUser( $items )
    {
        $user = new \app\models\UserVehicle();

        foreach ($items as $item) {
            $item->user = $user->find($item->user_id);
        }

        return $items;
    }

    /**
     * @param array|null $request
     */
    public function index( array $request = null )
    {
        responseJson( $this->addUser( $this->model->selectBy([ 'user_id' => $request['user_id']]) ) );
    }


    /**
     * @param array $request
     * @return mixed
     * @throws Exception
     */
    public function create( array $request )
    {

        $dealership = $this->model->create($request);

        return responseJson( $this->addUser([$dealership])[0] );
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
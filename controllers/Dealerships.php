<?php


class Dealerships
{
    public $model;

    /**
     * Dealerships constructor.
     */
    public function __construct()
    {
        $this->model = new \app\models\Dealership();
    }

    /**
     * @param $items
     * @return mixed
     */
    private function addPrices( $items )
    {
        $price = new \app\models\DealershipPrice();

        foreach ($items as $item)
        {
            $item->prices = $price->selectBy(['dealership_id' => $item->id]);
        }

        return $items;
    }

    /**
     * @param $prices
     * @param $dealership_id
     * @throws Exception
     */
    private function createPrice( $prices, $dealership_id )
    {
        $price = new \app\models\DealershipPrice();
        

        foreach ($prices as $item) {
            $item['dealership_id'] = $dealership_id;
            $price->create($item);
        }
    }

    /**
     * @param array|null $request
     */
    public function index( array $request = null )
    {
        responseJson( $this->addPrices($this->model->index() ) );
    }

    /**
     * @param null $array
     * @return mixed
     */
    public function search( $array = null )
    {
        return responseJson( $this->addPrices($this->model->search($array['name'])) );
    }

    /**
     * @param array $request
     * @return mixed
     * @throws Exception
     */
    public function create( array $request )
    {
        $prices = [];
        if($request['prices']) {
            $prices = $request['prices'];
            unset($request['prices']);
        }

        $dealership = $this->model->create($request);

        $this->createPrice($prices, $dealership->id);

        return responseJson( [$this->addPrices($dealership)][0] );
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

        return responseJson($this->model->deleteWitPrices($id));
    }


}
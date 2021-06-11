<?php

class Users
{

    /**
     * @param array $request
     * @return mixed
     * @throws Exception
     *
     */
    public function create( array $request )
    {
        $user = (new \app\models\User())->create($request);

        return responseJson($user);
    }

    /**
     * @param array $request
     * @return mixed
     */
    public function login( array $request )
    {
        if(isset($request['password']) && isset($request['email']))
        {
            $user = (new \app\models\User())->findBy([
                'email' => $request['email']
            ]);

            if($user->password == $request['password']) {
                return responseJson($user);
            }
        }

        return responseJson([
            'error' => 'invalid data'
        ]);
    }

}
<?php

namespace Elpsy\Repository\Contacts;
/**
 * Interface PresenterInterface
 * @package Prettus\Repository\Contracts
 */
interface PresenterInterface
{

    public function boot();

    public function include($include);

    public function pushInclude($include);

    public function serializer();

    public function getTransformer();

    public function present($data);

}
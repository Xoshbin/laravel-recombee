<?php

use Baron\Recombee\Facades\Recombee;
use Baron\Recombee\Support\InteractionCollection;
use Baron\Recombee\Tests\Fixtures\Item;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\AddPurchase;
use Recombee\RecommApi\Requests\DeletePurchase;
use Recombee\RecommApi\Requests\ListItemPurchases;
use Recombee\RecommApi\Requests\ListUserPurchases;

it('can add a purchase of a given item made by a given user', function () {
    $this->mock(Client::class)
        ->shouldReceive('send')
        ->once()
        ->with(AddPurchase::class)
        ->andReturn('ok');

    $results = Recombee::for(2)->purchased(509)->save();

    expect($results)->toBeTrue();
});

it('can delete all purchases of a given item made by a given user', function () {
    $this->mock(Client::class)
        ->shouldReceive('send')
        ->once()
        ->with(DeletePurchase::class)
        ->andReturn('ok');

    $results = Recombee::for(2)->purchased(509)->delete();

    expect($results)->toBeTrue();
});

it('can list all purchases made by a given user', function () {
    $interactions = [[
        'itemId' => '509',
        'userId' => '3',
        'timestamp' => 1634971162,
        'amount' => 1.0,
    ]];

    $this->mock(Client::class)
        ->shouldReceive('send')
        ->once()
        ->with(ListUserPurchases::class)
        ->andReturn($interactions);

    $results = Recombee::for(3)->purchases()->get();

    expect($results instanceof InteractionCollection)->toBeTrue();
    expect($results->collection->all())->toEqual($interactions);
});

it('can list all the ever-made purchases of a given item', function () {
    $interactions = [[
        'userId' => '3',
        'itemId' => '509',
        'timestamp' => 1634971162,
        'amount' => 1.0,
    ]];

    $this->mock(Client::class)
        ->shouldReceive('send')
        ->once()
        ->with(ListItemPurchases::class)
        ->andReturn($interactions);

    $results = Recombee::for(new Item(['id' => 509]))->purchases()->get();

    expect($results instanceof InteractionCollection)->toBeTrue();
    expect($results->collection->all())->toEqual($interactions);
});
<?php

it('can test using browserKit', function () {
    $this->registerTestRoute('render_components');

    try {
        $this->visit('/render_components');
    } catch (Throwable $e) {
        echo $e->getMessage();
        echo $e->getFile() . ':' . $e->getLine();
        throw $e;
    }
})->skip();

it('can render components', function () {
    $this->registerTestRoute('render_components')
        ->visit('/render_components')

        // methods
        ->seeElement('form[id="form_get"][method="GET"]')
        ->seeElement('form[id="form_post"][method="POST"]')
        ->seeElement('form[id="form_put"][method="POST"]')
        ->seeElement('form[id="form_patch"][method="POST"]')
        ->seeElement('form[id="form_delete"][method="POST"]')

        // spoofing
        ->dontSeeElement('form[id="form_get"] input[name="_method"]')
        ->dontSeeElement('form[id="form_post"] input[name="_method"]')
        ->seeElement('form[id="form_put"] input[name="_method"][value="PUT"]')
        ->seeElement('form[id="form_patch"] input[name="_method"][value="PATCH"]')
        ->seeElement('form[id="form_delete"] input[name="_method"][value="DELETE"]');
})->skip();

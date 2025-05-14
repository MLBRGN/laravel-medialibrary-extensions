<?php

// TODO check if components loaded:
// https://stackoverflow.com/questions/64095795/how-to-check-if-laravel-blade-component-is-loaded
it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

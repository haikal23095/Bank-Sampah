<?php

test('the application returns a successful response', function () {
    // Follow redirects so test passes whether '/' returns 200 or redirects (e.g., to login)
    $response = $this->followingRedirects()->get('/');

    $response->assertStatus(200);
});

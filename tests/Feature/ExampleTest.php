<?php

test('returns a successful response', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});

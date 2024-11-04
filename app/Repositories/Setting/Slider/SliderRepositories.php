<?php

namespace App\Repositories\Setting\Slider;

interface SliderRepositories
{
    public function data();
    public function store(array $request);
    public function update(array $request, string $uuidSlider);
    public function get(string $uuidSlider);
    public function delete(string $uuidSlider);
}

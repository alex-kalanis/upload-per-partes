<?php

namespace Support;


use kalanis\UploadPerPartes\Interfaces\IDriverLocation;
use kalanis\UploadPerPartes\ServerKeys\AKey;


class ServerKey extends AKey
{
    public function fromData(IDriverLocation $data): string
    {
        return 'php://memory' . '/' . $data->getDriverKey();
    }
}

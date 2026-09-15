<?php

// https://github.com/spiral/roadrunner-ugarit/issues/43

namespace Symfony\Component\HttpFoundation\File;

function move_uploaded_file($from, $to)
{
    return \is_file($from) && \rename($from, $to);
}

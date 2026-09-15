<?php

namespace Ugarit\Octane\Listeners;

use Exception;
use Heritage\Contracts\Debug\ExceptionHandler;
use Ugarit\Octane\Events\WorkerErrorOccurred;
use Ugarit\Octane\Exceptions\DdException;
use Ugarit\Octane\Stream;
use Ugarit\Octane\Tests\TestCase;
use Mockery;

class ReportExceptionTest extends TestCase
{
    public function test_exceptions_are_streamed()
    {
        [$app, $worker] = $this->createOctaneContext([]);

        $exception = new Exception('foo');

        Mockery::mock('alias:'.Stream::class)
            ->shouldReceive('throwable')
            ->once()
            ->with($exception);

        $worker->dispatchEvent($app, new WorkerErrorOccurred($exception, $app));
    }

    public function test_exceptions_are_reported()
    {
        [$app, $worker] = $this->createOctaneContext([]);

        $exception = new Exception('foo');

        $exceptionHandler = tap(Mockery::mock(ExceptionHandler::class), fn ($mock) => $mock
            ->shouldReceive('report')
            ->once()
            ->with($exception));

        Mockery::mock('alias:'.Stream::class)
            ->shouldReceive('throwable')
            ->once()
            ->with($exception);

        $app->bind(ExceptionHandler::class, fn () => $exceptionHandler);

        $worker->dispatchEvent($app, new WorkerErrorOccurred($exception, $app));
    }

    public function test_dd_calls_are_not_streamed()
    {
        [$app, $worker] = $this->createOctaneContext([]);

        $exception = new DdException(['foo' => 'bar']);

        Mockery::mock('alias:'.Stream::class)
            ->shouldReceive('throwable')
            ->never();

        $worker->dispatchEvent($app, new WorkerErrorOccurred($exception, $app));
    }
}

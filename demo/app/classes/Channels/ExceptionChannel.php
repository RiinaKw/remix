<?php

namespace RemixDemo\Channels;

use Remix\Channel;
use Error;
use RuntimeException;
use Remix\RemixException;
use Remix\Exceptions\{
    AppException,
    CoreException,
    DJException,
    ErrorException
};
use Remix\Exceptions\Http\{
    HttpNotFoundException,
    HttpMethodNotAllowedException
};

/**
 * Channel to test for exception behavior.
 *
 * @package  Demo\Channels
 */
class ExceptionChannel extends Channel
{
    /**
     * Throws HttpNotFoundException
     */
    public function throw404(): string
    {
        throw new HttpNotFoundException('test 404 exception');
        return __METHOD__;
    }
    // function throw404()

    /**
     * Throws HttpMethodNotAllowedException
     */
    public function throw405(): string
    {
        throw new HttpMethodNotAllowedException('test 405 exception');
        return __METHOD__;
    }
    // function throw405()

    /**
     * Throws RemixException
     */
    public function throwRemix(): string
    {
        throw new RemixException('test Remix exception');
        return __METHOD__;
    }
    // function throwApp()

    /**
     * Throws AppException
     */
    public function throwApp(): string
    {
        throw new AppException('test App exception');
        return __METHOD__;
    }
    // function throwApp()

    /**
     * Throws CoreException
     */
    public function throwCore(): string
    {
        throw new CoreException('test Core exception');
        return __METHOD__;
    }
    // function throwCore()

    /**
     * Throws DJException
     */
    public function throwDJ(): string
    {
        throw new DJException('test DJ exception');
        return __METHOD__;
    }
    // function throwDJ()

    /**
     * Throws DJException
     */
    public function throwError(): string
    {
        throw new ErrorException('test Error exception');
        return __METHOD__;
    }
    // function throwError()

    /**
     * Throws DJException
     */
    public function throwRuntime(): string
    {
        throw new RuntimeException('test Runtime exception');
        return __METHOD__;
    }
    // function throwRuntime()

    /**
     * Throws Error
     */
    public function throwNativeError(): string
    {
        throw new Error('test Error');
        return __METHOD__;
    }
    // function throwNativeError()

    /**
     * Contains syntax errors
     */
    public function throwSyntax(): string
    {
        boo;
        return __METHOD__;
    }
    // function throwSyntax()

    /**
     * trigger_error E_USER_ERROR
     */
    public function throwTriggerError(): string
    {
        trigger_error('test trigger error', E_USER_ERROR);
        return __METHOD__;
    }
    // function throwTriggerError()

    /**
     * trigger_error E_USER_ERROR
     */
    public function throwTriggerWarning(): string
    {
        trigger_error('test trigger warning', E_USER_WARNING);
        return __METHOD__;
    }
    // function throwTriggerWarning()

    /**
     * trigger_error E_USER_ERROR
     */
    public function throwTriggerNotice(): string
    {
        trigger_error('test trigger notice', E_USER_NOTICE);
        return __METHOD__;
    }
    // function throwTriggerNotice()
}
// class TopChannel

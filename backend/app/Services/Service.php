<?php

namespace App\Services;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

abstract class Service
{
    /**
     * Data to be used in the service.
     * @var array
     */
    protected $data;

    /**
     * Keys or fields that should be present on
     * the data.
     * @var array
     */
    protected $keys = [];

    /**
     * The model to use.
     * @var Model
     */
    protected $model;

    /**
     * The message if authentication fails.
     * @var array
     */
    protected $message = '';

    /**
     * The status code to show.
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Create a new instance of the service.
     * @param mixed|null $data
     */
    public function __construct($data = null)
    {
        if ($data) {
            $this->validate($data);
        }
        $this->data = $data;
    }

    /**
     * Set data and return the service instance.
     * @param array $data
     * @return static
     */
    public function withData($data)
    {
        $this->validate($data);
        $this->data = $data;
        return $this;
    }

    /**
     * Validate given data.
     * @param mixed $data
     * @throws BadMethodCallException
     */
    protected function validate($data)
    {
        $errors = [];
        foreach ($this->keys as $key) {
            if (!in_array($key, array_keys($data))) {
                $errors[] = Str::title(str_replace('_', ' ', $key));
            }
        }
        if (!empty($errors)) {
            throw new BadMethodCallException(implode(', ', $errors) . ' is required.');
        }
        return $this;
    }

    /**
     * Get a value from the data.
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->data[$key];
    }

    /**
     * Get the message.
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the model.
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Create a HTTP Response with the error message.
     * @param int $statusCode
     * @return Response
     */
    public function asErrorResponse($statusCode = null)
    {
        return new Response([
            'message' => $this->getMessage(),
            'errors' => [],
        ], $statusCode ? $statusCode : $this->statusCode);
    }

    /**
     * Create a HTTP Response.
     * @param int $statusCode
     * @return Response
     */
    public function asResponse($statusCode = null)
    {
        return new Response(
            $this->model || '',
            $statusCode
                ? $statusCode
                : $this->statusCode
        );
    }

    /**
     * Dynamically get data.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}

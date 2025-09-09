<?php

namespace App\Telegram\Types\TelegramRequest;


/**
 * Contains methods that must be implemented by all Telegram methods
 */
abstract class TelegramMethods
{
    public string $methodName;
    protected TelegramResponse $response;

    /**
     * Exports the class to an array in order to send it to the Telegram servers without extra fields that we don't need
     *
     * @return array
     */
    final public function export(): array
    {
        $finalArray = [];
        try
        {
            try
            {
                $reflectionClass = new \ReflectionClass(get_class($this));
                if ($reflectionClass->hasMethod('formatting'))
                {
                    $this->formatting();
                }
                if ($reflectionClass->hasMethod('__construct'))
                {
                    $reflectionMethod = $reflectionClass->getMethod('__construct');
                    $params = $reflectionMethod->getParameters();
                    $hasParametrClear = false;
                    foreach ($params as $param)
                    {
                        try
                        {
                            if ($param->getName() == "clear")
                            {
                                $hasParametrClear = true;
                                break;
                            }
                        } catch (\Throwable $throwable)
                        {
                            logging($throwable);
                        }
                    }

                    $cleanObject = $hasParametrClear ? new $this(clear: true) : new $this();
                }
                else
                    $cleanObject = new $this();

                if (empty($cleanObject->methodName))
                    return [];
            } catch (\Throwable $throwable)
            {
                logging($throwable);
                $cleanObject = new $this();
            }
            foreach ($this as $fieldId => $value)
            {
                if (isset($cleanObject->$fieldId) && $this->$fieldId === $cleanObject->$fieldId) continue;

                if (is_object($this->$fieldId) && method_exists(get_class($this->$fieldId), "get"))
                {
                    $finalArray[$fieldId] = $this->$fieldId->get();
                }
                else if (is_object($value) && method_exists(get_class($this->$fieldId), "get_" . $fieldId))
                {
                    $method_name = "get_" . $fieldId;
                    $finalArray[$fieldId] = $this->$fieldId->$method_name();
                }
                else $finalArray[$fieldId] = $this->$fieldId;
            }
        } catch
        (\Throwable $throwable)
        {
            logging($throwable);
        }
        return $finalArray;
    }

    final protected function getResult(): TelegramResult
    {
        return new TelegramResult($this->response);
    }
}

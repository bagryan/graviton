<?php
/**
 * GetResponseListener for parsing Accept-Language headers
 */

namespace Graviton\RestBundle\Listener;

use Graviton\ExceptionBundle\Exception\MalformedInputException;
use Symfony\Component\EventDispatcher\Event;
use Graviton\ExceptionBundle\Exception\ValidationException;
use Graviton\RestBundle\Event\RestEvent;
use Graviton\ExceptionBundle\Exception\NoInputException;
use Graviton\RestBundle\Validator\JsonInput;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * GetResponseListener for parsing Accept-Language headers
 *
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class ValidationRequestListener
{
    /**
     * Validator
     *
     * @var JsonInput
     */
    private $jsonInput;

    /**
     * Create ValidationRequestListener with JsonInput validator
     *
     * @param JsonInput $jsonInput json input validator
     */
    public function __construct(JsonInput $jsonInput)
    {
        $this->jsonInput = $jsonInput;
    }

    /**
     * Validate the json input to prevent errors in the following components
     *
     * @param Event $event Event
     *
     * @throws NoInputException
     * @throws ValidationException
     * @throws \Exception
     * @return RestEvent
     */
    public function onKernelRequest(Event $event)
    {
        // only validate on POST and PUT
        // if PATCH is required, refactor the method or do something else
        $request = $event->getRequest();

        $content = $request->getContent();
        if (empty($content)) {
            $isJson = true;
        } else {
            $isJson = strtolower(substr($request->headers->get('content-type'), 0, 16)) == 'application/json';
        }
        if ($isJson && in_array($request->getMethod(), array('POST', 'PUT'))) {
            $controller = $event->getController();

            // Moved this from RestController to ValidationListener (don't know if necessary)
            if (is_resource($content)) {
                throw new BadRequestHttpException('unexpected resource in validation');
            }

            // Decode the json from request
            if (!($input = json_decode($content, true)) && JSON_ERROR_NONE === json_last_error()) {
                $e = new NoInputException();
                $e->setResponse($event->getResponse());
                throw $e;
            }

            // specially check for parse error ($input decodes to null) and report accordingly..
            if (is_null($input) && JSON_ERROR_NONE !== json_last_error()) {
                $e = new MalformedInputException($this->getLastJsonErrorMessage());
                $e->setErrorType(json_last_error());
                $e->setResponse($event->getResponse());
                throw $e;
            }

            // get the input validator
            $this->jsonInput->setRequest($request);

            // get the document manager for this model
            $em = $controller->getModel()->getRepository()->getDocumentManager();
            $this->jsonInput->setDocumentManager($em);

            // validate the document
            $result = $this->jsonInput->validate($input, $controller->getModel()->getEntityClass());

            if ($result->count() > 0) {
                // $response->send()...
                $e = new ValidationException("Validation failed");
                $e->setViolations($result);

                // pass the event..???
                $e->setResponse($event->getResponse());

                throw $e;
            }
        }

        return $event;
    }

    /**
     * Used for backwards compatibility to PHP 5.4
     *
     * @return string
     */
    private function getLastJsonErrorMessage()
    {
        $message = 'Unable to decode JSON string';

        if (function_exists('json_last_error_msg')) {
            $message = json_last_error_msg();
        }

        return $message;
    }
}

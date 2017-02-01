<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller-plugin-forms
 * @author: n3vra
 * Date: 1/25/2017
 * Time: 2:36 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin\Forms;

use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginInterface;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\FormElementManager;
use Interop\Container\ContainerInterface;
use Zend\Form\Form;
use Zend\Form\FormInterface;

/**
 * Class FormsPlugin
 * @package Dot\Controller\Plugin\Forms
 */
class FormsPlugin implements PluginInterface
{
    /** @var  FormElementManager */
    protected $formElementManager;

    /** @var  ContainerInterface */
    protected $container;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /**
     * FormsPlugin constructor.
     * @param FormElementManager $formManager
     * @param ContainerInterface $container
     * @param FlashMessengerInterface|null $flashMessenger
     */
    public function __construct(
        FormElementManager $formManager,
        ContainerInterface $container,
        FlashMessengerInterface $flashMessenger = null
    ) {
        $this->formElementManager = $formManager;
        $this->container = $container;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param string $formName
     * @return object
     */
    public function __invoke(string $formName = null): object
    {
        if (is_null($formName)) {
            return $this;
        }

        $form = null;
        // check the container first, in case there is a form to get through the abstract factory
        $abstractFormName = FormAbstractServiceFactory::PREFIX . '.' . $formName;
        if ($this->container->has($abstractFormName)) {
            $form = $this->container->get($abstractFormName);
        } elseif ($this->formElementManager->has($formName)) {
            $form = $this->formElementManager->get($formName);
        }

        if (!$form) {
            throw new RuntimeException(
                "Form with name $formName could not be created. Are you sure you registered it in the form manager?"
            );
        }

        $this->restoreFormState($form);
        return $form;
    }

    /**
     * @param Form $form
     */
    public function restoreFormState(Form $form)
    {
        if ($this->flashMessenger) {
            $dataKey = $form->getName() . '_data';
            $messagesKey = $form->getName() . '_messages';

            $data = $this->flashMessenger->getData($dataKey) ?: [];
            $messages = $this->flashMessenger->getData($messagesKey) ?: [];

            $form->setData($data);
            $form->setMessages($messages);
        }
    }

    /**
     * @param Form $form
     */
    public function saveFormState(Form $form)
    {
        if ($this->flashMessenger) {
            $dataKey = $form->getName() . '_data';
            $messagesKey = $form->getName() . '_messages';

            $this->flashMessenger->addData($dataKey, $form->getData(FormInterface::VALUES_AS_ARRAY));
            $this->flashMessenger->addData($messagesKey, $form->getMessages());
        }
    }

    /**
     * @param Form $form
     * @return array
     */
    public function getFormMessages(Form $form): array
    {
        $formMessages = $form->getMessages();
        return $this->processFormMessages($formMessages);
    }

    /**
     * @param array $formMessages
     * @return array
     */
    protected function processFormMessages(array $formMessages): array
    {
        $messages = [];
        foreach ($formMessages as $message) {
            if (is_array($message)) {
                foreach ($message as $m) {
                    if (is_string($m)) {
                        $messages[] = $m;
                    } elseif (is_array($m)) {
                        $messages = array_merge($messages, $this->processFormMessages($m));
                    }
                }
            } elseif (is_string($message)) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * @param Form $form
     * @return array
     */
    public function getFormErrors(Form $form): array
    {
        $formMessages = $form->getMessages();
        return $this->processFormErrors($formMessages);
    }

    /**
     * @param array $formMessages
     * @return array
     */
    protected function processFormErrors(array $formMessages): array
    {
        $errors = [];
        foreach ($formMessages as $key => $message) {
            if (is_array($message)) {
                if (!isset($errors[$key])) {
                    $errors[$key] = array();
                }

                foreach ($message as $k => $m) {
                    if (is_string($m)) {
                        $errors[$key][] = $m;
                    } elseif (is_array($m)) {
                        $errors[$key][$k] = $this->processFormErrors($m);
                    }
                }
            } elseif (is_string($message)) {
                $errors[] = $message;
            }
        }

        return $errors;
    }
}

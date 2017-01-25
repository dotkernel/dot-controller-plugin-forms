<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller-plugin-forms
 * @author: n3vra
 * Date: 1/25/2017
 * Time: 2:36 PM
 */

namespace Dot\Controller\Plugin\Forms;

use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginInterface;
use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\FormElementManager;
use Interop\Container\ContainerInterface;

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

    /**
     * FormsPlugin constructor.
     * @param FormElementManager $formManager
     * @param ContainerInterface $container
     */
    public function __construct(FormElementManager $formManager, ContainerInterface $container)
    {
        $this->formElementManager = $formManager;
        $this->container = $container;
    }

    /**
     * @param string $formName
     * @return object
     */
    public function __invoke($formName)
    {
        // check the container first, in case there is a form to get through the abstract factory
        $abstractFormName = FormAbstractServiceFactory::PREFIX . '.' . $formName;
        if ($this->container->has($abstractFormName)) {
            return $this->container->get($abstractFormName);
        }

        if ($this->formElementManager->has($formName)) {
            return $this->formElementManager->get($formName);
        }

        throw new RuntimeException("Form with name $formName could not be created. Are you sure you registered it?");
    }
}

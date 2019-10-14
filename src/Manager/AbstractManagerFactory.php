<?php

namespace ImkCrudBundle\Manager;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractManagerFactory.
 */
abstract class AbstractManagerFactory
{
    private $manager;
    /**
     * @var FormBuilderInterface
     */
    private $formBuilder;
    /**
     * @var Service
     */
    private $class;

    /**
     * @var FormBuilderInterface
     */
    private $form;

    /**
     * @var bool
     */
    private $redirect = false;
    /**
     * @var bool
     */
    private $update;

    /**
     * @var string
     */
    protected $className;

    protected $id = null;
    /**
     * @var bool
     */
    private $state;

    /**
     * ServiceClassService constructor.
     *
     * @param FormFactory $formFactory
     * @param EntityManagerInterface $manager
     * @throws \Exception
     */
    public function __construct(FormFactory $formFactory, EntityManagerInterface $manager)
    {
        $this->formBuilder = $formFactory;
        $this->manager = $manager;
    }

    /**
     * @param string $className
     *
     * @return object[]
     */
    public function list(string $className = null)
    {
        (!is_null($className)) ? $this->className = $className : $this->className;

        return $this->manager->getRepository($this->className)->findAll();
    }

    /**
     * Function insert.
     */
    public function insert($data)
    {
        $this->manager->persist($data);
        $this->manager->flush();
        $this->redirect = true;
        $this->state = true;

        return $this;
    }

    /**
     * Function delete.
     */
    public function delete(): self
    {
        $this->manager->remove($this->class);
        $this->manager->flush();
        $this->redirect = true;

        return $this;
    }

    /**
     * @return object|null
     */
    public function show()
    {
        return $this->manager->getRepository($this->className)->find($this->class->getId());
    }


    /**
     * @param int|null $id
     *
     * @return AbstractManagerFactory
     */
    public function fetch(int $id = null): self
    {
        $this->id = $id;
        if (is_null($this->id)) {
            $this->class = new $this->className();
            $this->update = false;

            return $this;
        }

        if (is_int($this->id)) {
            $this->class = $this->manager->getRepository($this->className)->find($this->id);
            $this->update = true;

            return $this;
        }

        return $this;
    }

    public function newInstance()
    {
        return $this->fetch();
    }

    /**
     * @return bool
     * @deprecated "will be replace by state"
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    public function getState()
    {
        return $this->redirect;
    }

    abstract public function setClass(string $className);

    /**
     * @param string $formType
     * @param Request $request
     *
     * @return mixed
     */
    public function formProcess(string $formType, Request $request)
    {
        $this->form = $this->formBuilder->create($formType, $this->class);
        $this->form->handleRequest($request);

        if ($this->form->isSubmitted()) {
            if (!$this->update) {
                $this->manager->persist($this->class);
            }
            $this->manager->flush();
            $this->redirect = true;
            $this->state = true;
        }

        return $this->form;
    }


}

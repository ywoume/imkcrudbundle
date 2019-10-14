<?php

namespace ImkCrudBundle\Manager;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    private $files = [];

    private $currentFile;

    private $uploadDir;

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
    public function formProcess(string $formType, Request $request, $uploadDir = null)
    {
        $this->form = $this->formBuilder->create($formType, $this->class);
        $this->form = $this->form->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {


            if (!is_null($uploadDir)) {
                $this->uploadDir = $uploadDir;

                $this->sniperFile($this->form->getData());
            }
            if (!$this->update) {
                $this->manager->persist($this->class);
            }

            $this->manager->flush();
            $this->redirect = true;
            $this->state = true;
        }

        return $this->form;
    }

    public function sniperFile()
    {
        $formMethods = get_class_methods(get_class($this->form->getData()));
        foreach ($formMethods as $key => $method) {

            if ('get' == substr($method, 0, 3)) {
                $methodVar = $this->form->getData()->$method();

                if (!is_null($methodVar) && is_string($methodVar)) {

                    if (substr(
                            $this->form->getData()->$method(),
                            0,
                            5
                        ) == '/tmp/') {
                        $setter = str_replace('get', 'set', $method);
                        $attribute = str_replace('get', '', $method);

                        $nameFile = $this->moveFile($this->form[lcfirst($attribute)]->getData());

                        $this->form->getData()->$setter($nameFile);
                    }

                }
            }
        }
    }



    private function moveFile(UploadedFile $item)
    {

        $originalFilename = pathinfo($item->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $newFilename = $safeFilename.'-'.uniqid().'.'.$item->guessExtension();

        try {
            $item->move(
                $this->uploadDir,
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            throw new FileException($e->getCode().'-  '.$e->getMessage());
        }
    }

}

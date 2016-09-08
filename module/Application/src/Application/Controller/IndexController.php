<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $beers = $this->getServiceLocator()
                      ->get('Application\Model\BeerTableGateway')
                      ->fetchAll();
        return new ViewModel(array('beers' => $beers));
    }

    public function insertAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\Beer');
        $form->setAttribute('action', '/insert');
        $form->get('send')->setAttribute('value', 'Salvar');
        $tableGateway = $this->getServiceLocator()->get('Application\Model\BeerTableGateway');
        $beer = new \Application\Model\Beer;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($beer->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $beer->exchangeArray($data);
                $tableGateway->save($beer);
                return $this->redirect()->toUrl('/');
            }
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id > 0) { 
            $post = $tableGateway->get($id);
            $form->bind($post);
            $form->get('send')->setAttribute('value', 'Editar');
        }

        return new ViewModel(['beerForm' => $form]);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id == 0) {
            throw new \Exception("O código é obrigatório");
        }

        $tableGateway = $this->getServiceLocator()->get('Application\Model\BeerTableGateway');
        $tableGateway->delete($id);
        
        return $this->redirect()->toUrl('/');
    }
}

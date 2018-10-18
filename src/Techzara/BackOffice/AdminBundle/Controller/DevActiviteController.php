<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 10/17/18
 * Time: 7:11 PM
 */

namespace App\Techzara\BackOffice\AdminBundle\Controller;



use App\Techzara\Service\MetierManagerBundle\Entity\DevActivite;
use App\Techzara\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DevActiviteController
 * @package App\Techzara\BackOffice\AdminBundle\Controller
 */
class DevActiviteController extends Controller
{
    public function indexAction()
    {
        $_activite_manager = $this->get(ServiceName::SRV_METIER_ACTIVITE);
        $_activite_liste = $_activite_manager->getAllActivite();

        return $this->render('AdminBundle:DevActivite:index.html.twig',array(
            'activites' => $_activite_liste
        ));
    }


    /**
     * Affichage page modification activite
     * @param DevActivite $activite
     * @return Render page
     */
    public function editAction( DevActivite $activite )
    {
        if (!$activite) {
            throw $this->createNotFoundException('Unable to find DevActivite entity.');
        }

        $_edit_form = $this->createEditForm($activite);

        return $this->render('AdminBundle:DevActivite:edit.html.twig', array(
            'activite'    => $activite,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création activite
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $activite_manager = $this->get(ServiceName::SRV_METIER_ACTIVITE);

        $activite = new DevActivite();
        $_form   = $this->createCreateForm($activite);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement activite
            $activite_manager->saveActivite($activite, 'new');

            $activite_manager->setFlash('success', "Activite ajouté");

            return $this->redirect($this->generateUrl('activite_index'));
        }

        return $this->render('AdminBundle:DevActivite:add.html.twig', array(
            'activite' => $activite,
            'form'   => $_form->createView(),
        ));
    }

    /**
     * Modification activite
     * @param Request $_request requête
     * @param DevActivite $activite
     * @return Render page
     */
    public function updateAction(Request $_request, DevActivite $activite)
    {
        // Récupérer manager
        $_activite_manager = $this->get(ServiceName::SRV_METIER_ACTIVITE);

        if (!$activite) {
            throw $this->createNotFoundException('Unable to find DevClient entity.');
        }

        $_edit_form = $this->createEditForm($activite);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_activite_manager->saveActivite($activite, 'update');

            $_activite_manager->setFlash('success', "activite modifié");

            return $this->redirect($this->generateUrl('activite_index'));
        }

        return $this->render('AdminBundle:DevActivite:edit.html.twig', array(
            'activite'    => $activite,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition activite
     * @param DevActivite $activite The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DevActivite $activite)
    {
        $_form = $this->createForm(\App\Techzara\Service\MetierManagerBundle\Form\DevActivite::class, $activite, array(
            'action' => $this->generateUrl('activite_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création activite
     * @param DevActivite $activite The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DevActivite $activite)
    {
        $_form = $this->createForm(\App\Techzara\Service\MetierManagerBundle\Form\DevActivite::class, $activite, array(
            'action' => $this->generateUrl('activite_update', array('id' => $activite->getId())),
            'method' => 'PUT'
        ));

        return $_form;
    }

    /**
     * Suppression activite
     * @param Request $_request requête
     * @param DevActivite $activite
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, DevActivite $activite)
    {
        // Récupérer manager
        $activite_manager = $this->get(ServiceName::SRV_METIER_ACTIVITE);

        $_form = $this->createDeleteForm($activite);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression activite
            $activite_manager->deleteDevActivite($activite);

            $activite_manager->setFlash('success', 'Activite supprimé');
        }

        return $this->redirectToRoute('activite_index');
    }

    /**
     * Création formulaire de suppression activite
     * @param DevActivite $activite The DevActivite entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DevActivite $activite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('activite_delete', array('id' => $activite->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste activite
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $activite_manager = $this->get(ServiceName::SRV_METIER_ACTIVITE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            if ($_ids == null) {
                $activite_manager->setFlash('error', 'Veuillez sélectionner un élément à supprimer');

                return $this->redirect($this->generateUrl('activite_index'));
            }
            $activite_manager->deleteDevActiviteGroup($_ids);
        }

        $activite_manager->setFlash('success', 'Activite sélectionnés supprimés');

        return $this->redirect($this->generateUrl('activite_index'));
    }
}
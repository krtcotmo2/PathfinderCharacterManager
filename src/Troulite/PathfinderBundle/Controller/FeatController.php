<?php

/*
 * Copyright 2015 Jean-Guilhem Rouel
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Troulite\PathfinderBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Troulite\PathfinderBundle\Entity\Feat;
use Troulite\PathfinderBundle\Form\FeatType;

/**
 * Feat controller.
 *
 * @Route("/feats")
 */
class FeatController extends Controller
{

    /**
     * Lists all Feat entities.
     *
     * @Route("/", name="feats")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TroulitePathfinderBundle:Feat')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Feat entity.
     *
     * @Route("/{id}", name="feats_show")
     * @Method("GET")
     * @Template()
     *
     * @param Feat $feat
     *
     * @return array
     */
    public function showAction(Feat $feat)
    {
        return array(
            'entity'      => $feat,
        );
    }

    /**
     * @Route("/{id}/edit", name="feats_edit")
     * @Method({"GET", "PUT"})
     * @Template()
     *
     * @param Feat $feat
     * @param Request $request
     *
     * @return array
     */
    public function editAction(Feat $feat, Request $request)
    {
        $form = $this->createForm(
            new FeatType(),
            $feat,
            array('method' => 'PUT')
        );
        $form->add('submit', 'submit', array('label' => 'save'));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $feat . ' successfully updated');
            $this->redirectToRoute('feats_show', array('id' => $feat->getId()));
        }

        return array(
            'form'   => $form->createView(),
            'entity' => $feat
        );
    }
}

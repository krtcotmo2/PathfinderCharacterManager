<?php

namespace Troulite\PathfinderBundle\Controller;

use Doctrine\ORM\EntityManager;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Troulite\PathfinderBundle\Entity\Character;
use Troulite\PathfinderBundle\Entity\CharacterClassPower;
use Troulite\PathfinderBundle\Entity\CharacterFeat;
use Troulite\PathfinderBundle\Entity\ClassSpell;
use Troulite\PathfinderBundle\Entity\Level;
use Troulite\PathfinderBundle\Entity\SpellEffect;
use Troulite\PathfinderBundle\Form\BaseCharacterType;
use Troulite\PathfinderBundle\Form\CastSpellsType;
use Troulite\PathfinderBundle\Form\LevelUpFlow;
use Troulite\PathfinderBundle\Form\PowersActivationType;
use Troulite\PathfinderBundle\Form\SleepType;
use Troulite\PathfinderBundle\Form\UncastSpellsType;

/**
 * Character controller.
 *
 * @Route("/characters")
 */
class CharacterController extends Controller
{

    /**
     * Lists all Character entities.
     *
     * @Route("/", name="characters")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TroulitePathfinderBundle:Character')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a form to create a Character entity.
     *
     * @param Character $entity The entity
     * @return Form The form
     */
    private function createCreateForm(Character $entity)
    {
        $form = $this->createForm(
            new BaseCharacterType($this->container->getParameter('character_advancement')),
            $entity,
            array(
                'action' => $this->generateUrl('characters_new'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Character entity.
     *
     * @Route("/new", name="characters_new")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function newAction(Request $request)
    {
        $entity = new Character();
        $form = $this->createCreateForm($entity);

        if($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var $token TokenInterface */
                $token = $this->get('security.context')->getToken();
                $entity->setUser($token->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('characters_levelup', array('id' => $entity->getId())));
            }
        }

        $ability_scores = $this->container->getParameter('ability_scores');

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'ability_scores' => $ability_scores
        );
    }

    /**
     * Finds and displays a Character entity.
     *
     * @Route("/{id}", name="characters_show")
     * @Template()
     */
    public function showAction(Character $entity, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->get('troulite_pathfinder.character_bonuses')->applyAll($entity);

        $needActivationFeats = array();
        $passiveFeats = array();
        $otherFeats = array();
        foreach ($entity->getFeats() as $spellEffect) {
            if (!$spellEffect->getFeat()->hasEffects()) {
                $otherFeats[] = $spellEffect;
            } elseif (!$spellEffect->getFeat()->isPassive() || $spellEffect->getFeat()->hasExternalConditions()) {
                $needActivationFeats[] = $spellEffect;
            } else {
                $passiveFeats[] = $spellEffect;
            }
        }

        $needActivationClassPowers = array();
        $passiveClassPowers        = array();
        $otherClassPowers       = array();
        foreach ($entity->getClassPowers() as $classPower) {
            $power = $classPower->getClassPower();

            if ($power->hasEffects()) {
                $useless = 0;
                if (array_key_exists('feat', $power->getEffects())) {
                    $useless++;
                }
                if (array_key_exists('extra_feats', $power->getEffects())) {
                    $useless++;
                }

                if ($useless == count($power->getEffects())) {
                    // Don't add a power if it has not meaningful bonuses for the character sheet
                    continue;
                }

                if (!$power->isPassive() || $power->hasExternalConditions()) {
                    $needActivationClassPowers[] = $classPower;
                } else {
                    $passiveClassPowers[] = $classPower;
                }
            } else {
                $otherClassPowers[] = $classPower;
            }
        }

        $needActivationSpellEffects = array();
        $passiveSpellEffects        = array();
        $otherSpellEffects          = array();
        foreach ($entity->getSpellEffects() as $spellEffect) {
            $spell = $spellEffect->getSpell();
            if (!$spell->hasEffects()) {
                $otherSpellEffects[] = $spellEffect;
            } elseif (!$spell->isPassive() || $spell->hasExternalConditions()) {
                $needActivationSpellEffects[] = $spellEffect;
            } else {
                $passiveSpellEffects[] = $spellEffect;
            }
        }

        $powersActivationForm = $this->createForm(new PowersActivationType());
        $powersActivationForm->get('feats')->setData($needActivationFeats);
        $powersActivationForm->get('class_powers')->setData($needActivationClassPowers);
        $powersActivationForm->get('spell_effects')->setData($needActivationSpellEffects);
        $powersActivationForm->handleRequest($request);

        if ($powersActivationForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('characters_show', array('id' => $entity->getId())));
        }

        $castSpellsForm = $this->createForm(new CastSpellsType(), $entity);
        $castSpellsForm->handleRequest($request);
        if ($castSpellsForm->isValid()) {
            /** @var $f Form */
            foreach ($castSpellsForm->all() as $f) {
                if ($f->getName() === 'Cast') {
                    continue;
                }

                $spell = null;

                if ($f->getConfig()->getOption('spell')) { // Prepared Spell
                    $spell = $f->getConfig()->getOption('spell');
                } elseif ($f->getData()['spell']) { // Unprepared Spell
                    /** @var $classSpell ClassSpell */
                    $classSpell = $f->getData()['spell'];
                    $spell = $classSpell->getSpell();
                }
                $class = $f->getConfig()->getOption('class');
                $target = $f->getData()['targets'];

                if ($target === null || $spell === null) {
                    continue;
                }

                switch ($target) {
                    case 'other':
                        $this->get('troulite_pathfinder.spell_casting')->cast($entity, $spell, $class);
                        break;
                    case 'allies':
                        $this->get('troulite_pathfinder.spell_casting')->cast(
                            $entity,
                            $spell,
                            $class,
                            $entity->getParty()->getCharacters()
                        );
                        break;
                    default:
                        $target = $em->getRepository('TroulitePathfinderBundle:Character')->find($target);
                        if ($target) {
                            $this->get('troulite_pathfinder.spell_casting')->cast($entity, $spell, $class, array($target));
                        }
                        break;
                }

                return $this->redirect($this->generateUrl('characters_show', array('id' => $entity->getId())));
            }
        }

        $uncastSpellsForm = $this->createForm(new UncastSpellsType(), $entity);
        $uncastSpellsForm->handleRequest($request);
        if ($uncastSpellsForm->isValid()) {
            foreach ($uncastSpellsForm->all() as $f) {
                if ($f->getName() === 'Uncast') {
                    continue;
                }

                if ($f->getData()['uncast'] === true) {
                    /** @var $spellEffect SpellEffect */
                    $spellEffect = $f->getConfig()->getOption('spellEffect');
                    $spellEffect->getCharacter()->removeSpellEffect($spellEffect);
                }
            }
            $em->flush();

            return $this->redirect($this->generateUrl('characters_show', array('id' => $entity->getId())));
        }

        $skills = $em->getRepository('TroulitePathfinderBundle:Skill')->findAll();

        return array(
            'entity' => $entity,
            'powers_activation_form' => $powersActivationForm->createView(),
            'skills' => $skills,
            'passive_feats' => $passiveFeats,
            'passive_class_powers' => $passiveClassPowers,
            'other_feats' => $otherFeats,
            'other_class_powers' => $otherClassPowers,
            'passive_spell_effects' => $passiveSpellEffects,
            'other_spell_effects' => $otherSpellEffects,
            'castSpellsForm' => $castSpellsForm->createView(),
            'uncastSpellsForm' => $uncastSpellsForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Character entity.
     *
     * @Route("/{id}/edit", name="characters_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TroulitePathfinderBundle:Character')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Character entity.
     *
     * @param Character $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Character $entity)
    {
        $form = $this->createForm(
            new BaseCharacterType($this->container->getParameter('character_advancement')),
            $entity,
            array(
                'action' => $this->generateUrl('characters_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Character entity.
     *
     * @Route("/{id}/update", name="characters_update")
     * @Method("PUT")
     * @Template("TroulitePathfinderBundle:Character:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TroulitePathfinderBundle:Character')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Update successful');

            return $this->redirect($this->generateUrl('characters_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Character entity.
     *
     * @Route("/{id}/levelup", name="characters_levelup")
     * @Template()
     */
    public function levelUpAction(Character $entity)
    {
        $level = new Level();
        $entity->addLevel($level);
        $this->get('troulite_pathfinder.character_bonuses')->applyAll($entity);

        /** @var $flow LevelUpFlow */
        $flow = $this->get('troulite_pathfinder.form.flow.levelup');
        $flow->bind($level);

        // Add class powers if they were not already added through a form
        if ($level->getClassDefinition()) {
            foreach ($level->getClassDefinition()->getPowers($entity->getLevel($level->getClassDefinition())) as $power) {
                $alreadyAdded = false;
                foreach ($level->getClassPowers() as $classPower) {
                    if ($classPower->getClassPower() === $power) {
                        $alreadyAdded = true;
                        break;
                    }
                }
                if (!$alreadyAdded) {
                    $level->addClassPower((new CharacterClassPower())->setClassPower($power));
                }
            }
        }

        // Cleanup empty feats that may have been added by the form
        foreach ($level->getFeats() as $feat) {
            if ($feat === null || $feat->getFeat() === null) {
                $level->removeFeat($feat);
            }
        }

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                // flow finished

                // Cleanup empty skills as well
                foreach ($level->getSkills() as $levelSkill) {
                    if ($levelSkill->getValue() === 0) {
                        $level->removeSkill($levelSkill);
                    }
                }

                // Max HP for first level
                if ($entity->getLevel() === 1) {
                    $entity->getLevels()[0]->setHpRoll($entity->getLevels()[0]->getClassDefinition()->getHpDice());
                }

                /** @var $em EntityManager */
                $em = $this->getDoctrine()->getManager();

                // Add fixed extra feats granted by this level
                foreach ($level->getClassPowers() as $power) {
                    $effects = $power->getClassPower()->getEffects();
                    if (
                        $power->getClassPower()->hasEffects() &&
                        array_key_exists('feat', $effects) &&
                        $effects['feat']['type'] !== 'oneof'
                    ) {
                        $feat = $em->getRepository('TroulitePathfinderBundle:Feat')
                            ->findOneBy(array('name' => $effects['feat']['value']));
                        if ($feat) {
                            $level->addFeat((new CharacterFeat())->setFeat($feat));
                        }
                    }
                }

                $em->persist($level);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $entity . ' is now level ' . $entity->getLevel()
                );

                return $this->redirect($this->generateUrl('characters_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'flow' => $flow,
            'entity' => $entity
        );
    }

    /**
     * Deletes a Character entity.
     *
     * @Route("/{id}", name="characters_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TroulitePathfinderBundle:Character')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Character entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('characters'));
    }

    /**
     * Creates a form to delete a Character entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('characters_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * Deletes a Character entity.
     *
     * @Route("/{id}/sleep", name="characters_sleep")
     * @Method("GET|POST")
     * @Template()
     */
    public function sleepAction(Character $entity, Request $request)
    {
        /** @var $em EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        $sleepForm = $this->createForm(new SleepType(), $entity, array('em' => $em));

        $sleepForm->handleRequest($request);
        if ($sleepForm->isValid()) {
            $entity->setNonPreparedCastSpellsCount(null);
            foreach ($entity->getPreparedSpells() as $preparedSpell) {
                $preparedSpell->setAlreaydCast(false);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('characters_show', array('id' => $entity->getId())));
        }

        return array(
            'form' => $sleepForm->createView()
        );
    }
}

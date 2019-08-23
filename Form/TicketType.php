<?php

/*
 * This file is part of the Novo SGA project.
 *
 * (c) Rogerio Lino <rogeriolino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novosga\TriageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Novosga\Entity\Prioridade;
use Novosga\Entity\Servico;
use Novosga\Entity\ServicoUnidade;
use Novosga\Entity\Unidade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $unidade = $options['unidade'];
        
        $builder
            ->add('servico', EntityType::class, [
                'class' => Servico::class,
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er) use ($unidade) {
                    $qb = $er
                        ->createQueryBuilder('e')
                        ->join(ServicoUnidade::class, 'su', 'WITH', 'su.servico = e')
                        ->where('su.unidade = :unidade')
                        ->andWhere('su.ativo = TRUE')
                        ->setParameter('unidade', $unidade);
                    
                    return $qb;
                },
                'label' => 'form.ticket.unidade',
                'translation_domain' => 'NovosgaTriageBundle',
            ])
            ->add('prioridade', EntityType::class, [
                'class' => Prioridade::class,
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->where('e.ativo = TRUE');
                },
                'label' => 'form.ticket.prioridade',
                'translation_domain' => 'NovosgaTriageBundle',
            ])
            ->add('cliente', CustomerType::class)
        ;
    }

    public function getBlockPrefix()
    {
        return null;
    }
    
    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'csrf_protection' => false,
            ])
            ->setRequired(['unidade'])
            ->setAllowedTypes('unidade', [Unidade::class]);
    }
}

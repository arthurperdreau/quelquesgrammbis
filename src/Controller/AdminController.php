<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(UserRepository $userRepository, ChartBuilderInterface $chartBuilder, PostRepository $postRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
        }

        $today = new \DateTimeImmutable('today');
        $yesterday = $today->modify('-1 day');

        $todayCount = $postRepository->countByDate($today);
        $yesterdayCount = $postRepository->countByDate($yesterday);

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR); // Bar chart ici !

        $chart->setData([
            'labels' => [
                $yesterday->format('d/m/Y'),
                $today->format('d/m/Y')
            ],
            'datasets' => [
                [
                    'label' => 'Post number',
                    'backgroundColor' => [
                        'rgba(255, 205, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    'borderColor' => [
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    'borderWidth' => 1,
                    'data' => [$yesterdayCount, $todayCount],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1
                    ],
                ],
            ],
        ]);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $userRepository->findAll(),
            'chart' => $chart,
        ]);
    }

    #[Route('/promote/{id}', name: 'promote')]
    public function promote(User $user, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array("ROLE_ADMIN",$this->getUser()->getRoles())  ) {
            return $this->redirectToRoute('app_login');
        }
        if ($user) {
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/demote/{id}', name: 'demote')]
    public function demote(User $user, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array("ROLE_ADMIN",$this->getUser()->getRoles())  ) {
            return $this->redirectToRoute('app_login');
        }
        if ($user) {
            $user->setRoles([]);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }
}

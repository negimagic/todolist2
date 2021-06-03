<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Form\UpdateProjectType;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="project")
     */
    public function index(ProjectRepository $projectRepository, PaginatorInterface $paginator, Request $request): Response
    {           
        $data = $projectRepository->findAll();

        $projects = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos projets)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            2 // Nombre de résultats par page
        );
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/project/view/{id}", name="projectById")
     */
    public function projectById(Project $project): Response
    {   
        
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'project' => $project,
        ]);
    }

    /**
     * @Route("/project/add", name="add_project")
     */
    public function createProject(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $project->setUser($user);

            $project->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));;

            $project->setStartAt(
                $form->get('start_at')->getData()
            );
                
            $project->setTitle(
                $form->get('title')->getData()
            );
            
            $project->setDescription(
                $form->get('description')->getData()
            );

            $project->setStatus(0);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();


            return $this->redirectToRoute('user');
        }


        return $this->render('project/add.html.twig', [
            'controller_name' => 'ProjectController',
            'ProjectForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/delete/{id}", name="del_project")
     */
    public function deleteProject(Project $project): Response
    {

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($project);
        $manager->flush();

        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/project/update/{id}", name="upd_project")
     */
    public function updateProject(Request $request, Project $project): Response
    {
        $form = $this->createForm(UpdateProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setTitle(
                $form->get('title')->getData()
            );
            $project->setDescription(
                $form->get('description')->getData()
            );
            $project->setEndAt(
                $form->get('end_at')->getData()
            );
            $project->setStatus(
                $form->get('status')->getData()
            );
            $project->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();


            return $this->redirectToRoute('user');
        }

        return $this->render('project/upd.html.twig', [
            'controller_name' => 'ProjectController',
            'project' => $project,
            'UpdateForm' => $form->createView()
        ]);
    }

    public function fectchUsers(UserRepository $userRepository){
        $users = $userRepository->findAll();
    }



}

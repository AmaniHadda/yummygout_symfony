<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Blog;
use App\Entity\Classroom;
use App\Form\AvisfType;
use App\Form\BlogsType;
use App\Form\BlogsTypeType;
use App\Form\ClassroomType;
use App\Repository\BlogRepository;
use App\Repository\ClassroomRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
    /**
     * @Route("/affblog", name="affblog")
     */

    public function afficher(BlogRepository $repository ,PaginatorInterface $paginator,Request $request)
    {

        $Blogs = $repository->findAll();


        // Paginate the results of the query
        $Blog = $paginator->paginate(
        // Doctrine Query, not results
            $Blogs,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('blog/Affiche.html.twig', ['bl' => $Blog]);

    }

    /**
     * @Route("/affblogf", name="affblogf")
     */

    public function afficherf(BlogRepository $repository )
    {
        $Blog = $repository->findAll();


        return $this->render('blog/affichef.html.twig', ['bl' => $Blog]);

    }
    /**
     * @Route ("/d/{id}",name="d")
     */
    public function supprimer($id, BlogRepository $repository)
    {
        $Blog = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Blog);
        $em->flush();

        return $this->redirectToRoute('affblog');
    }
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route ("/blog/add",name="add")
     */

    public function add(Request $request)
    {
        $blog = new Blog();
        $form = $this->createForm(BlogsType::class, $blog);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );

            $blog->setImage($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();
            return $this->redirectToRoute('affblog');

        }
        return $this->render('blog/Add.html.twig', [
            'form' => $form->createView()
        ]);

    }
    /**
     * @Route ("/blog/update/{id}",name="update")
     */
    public function update(BlogRepository $repository, $id, Request $request)
    {
        $blog = $repository->find($id);
        $form = $this->createForm(BlogsType::class, $blog);
        $form->add('update', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );

            $blog->setImage($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("affblog");
        }
        return $this->render('blog/update.html.twig', [
            'formu' => $form->createView()
        ]);
    }
    /**
     * @Route ("/avis/addafff",name="addafff")
     */
    public function addf(Request $request)
    {
        $avis = new Avis();
        $form = $this->createForm(AvisfType::class, $avis);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($avis);
            $em->flush();
            return $this->redirectToRoute('affavisf');
        }
        return $this->render('avis/Addaf.html.twig', [
            'formaff' => $form->createView()
        ]);

    }
    /**
     * @Route("/trit", name="trit")
     */
    public function OrderBytitre(BlogRepository $repository,Request $request,PaginatorInterface $paginator)
    {
        $four = $repository->orderBytitre();

        // Paginate the results of the query
        $Blog = $paginator->paginate(
        // Doctrine Query, not results
            $four,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('blog/Affiche.html.twig',
            ['bl' => $Blog]);
    }

    /**
     * @Route("student/rechlike", name="rechlike")
     */
    public function rechercherlike(BlogRepository $repository,PaginatorInterface $paginator,Request $request): Response
    {
        $nscrech = $request->get('search');
        $students = $repository->SearchNSC($nscrech);

        // Paginate the results of the query
        $Blog = $paginator->paginate(
        // Doctrine Query, not results
            $students,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
        );
        return $this->render('blog/Affiche.html.twig', ['bl' => $Blog]);

        return $this->render('blog/Affiche.html.twig',
            ['bl' => $student]);

    }


}
<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\isCsrfTokenValid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\denyAccessUnlessGranted;

class BooksController extends AbstractController
{
    /**
     * @Route("/books", name="app_books_index",methods="GET")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBy([],['createdAt' =>'DESC']);

        return $this->render('books/indexBooks.html.twig', compact('books'));
    }

    
     /**
     * @Route("/books/create", name="app_books_create", methods="GET|POST")
     * @Security("is_granted('ROLE_USER') and user.isVerified()")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo):Response
    {
        $book = new Book;
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $book->setUser($this->getUser());
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Le livre a été créé avec succès');

            return $this->redirectToRoute('app_books_index');

        }

        return $this->render('books/createBooks.html.twig',[
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/books/{id<[0-9]+>}", name="app_books_show",methods="GET")
     */
    public function show(Book $book): Response

    {
        return $this->render('books/showBooks.html.twig.', compact('book'));
    }

     /**
     * @Route("/books/{id<[0-9]+>}/edit", name="app_books_edit",methods="GET|PUT")
     *  @Security("is_granted('BOOK_MANAGE', book)")
     */ 
    public function edit(Request $request,Book $book, EntityManagerInterface $em): Response

    {

        $form = $this->createForm(BookType::class, $book, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->flush();

            $this->addFlash('success', 'Le livre a été modifié avec succès');

            return $this->redirectToRoute('app_books_index');

        }
        return $this->render('books/editBooks.html.twig.', [
            'book' => $book,
            'form' => $form->createView()
        ]);

        
    }    
      /**
     * @Route("/books/{id<[0-9]+>}/delete", name="app_books_delete",methods="DELETE")
     * @IsGranted("BOOK_MANAGE", subject="book")
     */
    public function delete(Request $request,Book $book, EntityManagerInterface $em): Response

    { 


        

        if ($this->isCsrfTokenValid('book_deletion_' . $book->getId(), $request->request->get('csrf_token'))){

            $em->remove($book);
            $em->flush();

            $this->addFlash('info', 'Le livre a été supprimé avec succès');

        }
           

            return $this->redirectToRoute('app_books_index');

        }
        
   }

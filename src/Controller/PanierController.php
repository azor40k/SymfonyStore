<?php namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PanierController extends AbstractController {

    /**
     * @Route("/", name="panier")
     */
    public function index() {
        $em=$this->getDoctrine()->getManager();
        $cart=$em->getRepository(Panier::class)->findBy(['etat'=> false]);
        return $this->render('panier/index.html.twig', [ 'cart'=> $cart,
            ]);
    }


    /**
     * @Route("/buy", name="panier_buy")
     */
    public function buy(TranslatorInterface $translator) {
        $em=$this->getDoctrine()->getManager();
        $cart=$em->getRepository(Panier::class)->findBy(['etat'=> false]);

        foreach($cart as $item) {

            $x = $item->getQuantite();

            $quantite = $item->getProduit()->getQuantite() - $x;

            $item->getProduit()->setQuantite($quantite);


            //Mise Etat > True
            $item->setEtat(true);
            $item->setDateAchat(new \DateTime());

            $em->persist($item);
            $em->flush();
        }

        $this->addFlash("success", $translator->trans('file.produit.ok1'));
        return $this->redirectToRoute('commande');
    }


    /**
     * @Route("/{id}/remove", name="panier_remove")
     */
    public function delete(Panier $item=null, TranslatorInterface $translator) {
        if($item !=null) {
            $em=$this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

            $this->addFlash("success", $translator->trans('file.produit.ok2'));
        }

        return $this->redirectToRoute('panier');
    }
}

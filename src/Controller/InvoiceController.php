<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceController extends AbstractController
{
    #[Route('/invoice/{id}', name: 'app_invoice')]
    public function index(Security $security,$id,EntityManagerInterface $entityManager,Request $request): Response
    {
        $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(['id'=>$id]);

        if ($request->request->has('confirm')) {
            $html = $this->renderView('invoice/pdf.html.twig', [
                'controller_name' => 'InvoiceController',
                'invoice' => $invoice,
            ]);

            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($pdfOptions);

            // load html to dompdf
            $dompdf->loadHtml($html);

            // render PDF
            $dompdf->render();

            //save
            $pdfContent = $dompdf->output();
            $fileLocation = $_SERVER['DOCUMENT_ROOT'] . "/factures/";
            $fileName = "invoice_" . $invoice->getId() . ".pdf";
            $fileFullPath = $fileLocation . $fileName;

            //check
            if (!file_exists($fileLocation)) {
                mkdir($fileLocation, 0777, true);
            }

            file_put_contents($fileFullPath, $pdfContent);

            // 生成并输出 PDF 文件
            $dompdf->stream("invoice.pdf", [
                "Attachment" => false // 如果你希望 PDF 文件在浏览器中打开
            ]);
            exit();
        }

        return $this->render('invoice/index.html.twig', [
            'controller_name' => 'InvoiceController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'invoice' => $invoice,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Language;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
* Class LanguageController
*
* @package App\Controller
* @Route("/api/v1", name="language_api")
*/
class LanguageController extends AbstractController
{
    /**
    * @param LanguageRepository $languageRepository
    * @return JsonResponse
    * @Route("/languages", name="languages", methods={"GET"})
    */
    public function getLanguages(LanguageRepository $languageRepository) {
        $data = $languageRepository->findAll();
        
        $all_languages = array_map(function($lang) {
            $language =  [
                'name' => $lang->getName(),
                'creator' => $lang->getCreator(),
                'popularity' => $lang->getPopularity(),
            ];
            return $language;
        }, $data); 
        
        return $this->response($all_languages, 200);
    }
    
    /**
    * @param Request $request
    * @param EntityManagerInterface $entityManager
    * @param LanguageRepository $languageRepository
    * @return JsonResponse
    * @throws \Exception
    * @Route("/languages", name="languages_add", methods={"POST"})
    */
    public function addLanguage(Request $request, EntityManagerInterface $entityManager, LanguageRepository $languageRepository) {
        try {
            $request = $this->transformJsonBody($request);
            if (!$request || !$request->get('name') || !$request->get('creator') || !$request->get('popularity')){
                throw new \Exception();
            }
            $language = new Language();
            $language->setName($request->get('name'));
            $language->setCreator($request->get('creator'));
            $language->setPopularity($request->get('popularity'));
            
            $entityManager->persist($language);
            $entityManager->flush();
            $data = [
                'status' => 200,
                'success' => "Language added successfully",
            ];
            return $this->response($data);
        }
        catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data invalid",
            ];
            return $this->response($data, 422);
        }
    }
    
    /**
    * @param LanguageRepository $languageRepository
    * @param $id
    * @return JsonResponse
    * @Route("/languages/{id}", name="languages_get", methods={"GET"})
    */
    public function getLanguage(LanguageRepository $languageRepository, $id) {
        $lang = $languageRepository->findBy(['id' => $id]);
        if (!$lang){
            $data = [
                'status' => 404,
                'errors' => "Language not found",
            ];
            return $this->response($data, 404);
        }
        
        $language = array_map(function($lang) {
            $data =  [
                'name' => $lang->getName(),
                'creator' => $lang->getCreator(),
                'popularity' => $lang->getPopularity(),
            ];
            return $data;
        }, $lang);   
        return $this->response($language);
    }
    
    /**
    * @param Request $request
    * @param EntityManagerInterface $entityManager
    * @param LanguageRepository $languageRepository
    * @param $id
    * @return JsonResponse
    * @Route("/languages/{id}", name="languages_put", methods={"PUT"})
    */
    public function updateLanguage(Request $request, EntityManagerInterface $entityManager, LanguageRepository $languageRepository, $id) {
        try {
            $request = $this->transformJsonBody($request); 
            
            if (!$request || !$request->get('name') || !$request->get('creator') ||  !$request->get('popularity')) {
                throw new \Exception();
             } 
            $language = $languageRepository->find($id);
            if (!$language) {
                $data = [
                    'status' => 404,
                    'errors' => "Language not found",
                ];
                return $this->response($data, 404);
            }  
            $language->setName($request->get('name'));
            $language->setCreator($request->get('creator'));
            $language->setPopularity($request->get('popularity'));
            $entityManager->flush();
            $data = [
                'status' => 200,
                'errors' => "Language updated successfully",
             ];
            return $this->response($data);
         }
         catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data invalid",
            ];
            return $this->response($data, 422);
        }
    }

    /**
    * @param LanguageRepository $languageRepository
    * @param $id
    * @return JsonResponse
    * @Route("/languages/{id}", name="languages_delete", methods={"DELETE"})
    */
    public function deleteLanguage(EntityManagerInterface $entityManager, LanguageRepository $languageRepository, $id) {
        $language = $languageRepository->find($id); 
        if (!$language) {
            $data = [
                'status' => 404,
                'errors' => "Language not found",
            ];
            return $this->response($data, 404);
        }
        $entityManager->remove($language);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Language deleted successfully",
        ];
        return $this->response($data);
    }

    /**
    * Returns a JSON response
    *
    * @param array $data
    * @param $status
    * @param array $headers
    * @return JsonResponse
    */
    public function response($data, $status = 200, $headers = []) {
        return new JsonResponse($data, $status, $headers);
    } 
    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request) {
        $data = json_decode($request->getContent(), true); 
        if ($data === null) {
            return $request;
        }
        $request->request->replace($data);
        return $request;
    }
}




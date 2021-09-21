<?php

namespace Plugin\SmartEC3\Controller\Admin\Category;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;

use Plugin\SmartEC3\Repository\ConfigRepository;

use Eccube\Controller\AbstractController;

use Eccube\Repository\CategoryRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class SmartRegiCategoryController extends AbstractController
{

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SmartRegiController constructor.
     *
     * @param ConfigRepository $configRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        CategoryRepository $categoryRepository
    ){
        $this->configRepository = $configRepository;
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @Route("/%eccube_admin_route%/smart_ec3/category/all", name="admin_smart_category_all")
     */
    public function categoryAll(Request $request)
    {

        $Categories = $this->categoryRepository->getList(null);

        foreach($Categories as $TargetCategory){
            $event = new EventArgs(
                [
                    'TargetCategory' => $TargetCategory,
                ],
                $request
            );

            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE, $event);
        }

        return $this->redirectToRoute('admin_product_category');
    }

}
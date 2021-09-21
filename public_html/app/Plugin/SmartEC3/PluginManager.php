<?php

namespace Plugin\SmartEC3;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\Constant;
use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

// Entity
use Eccube\Entity\Block;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Entity\Master\DeviceType;
use Plugin\SmartEC3\Entity\Config;
use Plugin\SmartEC3\Entity\Master\SmartRegiStore;
use Plugin\SmartEC3\Entity\Master\SmartRegiPrice;
use Plugin\SmartEC3\Entity\Master\SmartRegiTax;

// Repository
use Plugin\SmartEC3\Repository\ConfigRepository;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\LayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\Master\DeviceTypeRepository;


class PluginManager extends AbstractPluginManager
{
    // プラグインのコンフィグ
    // 初期値なし
    
    // ブロック
    // 初期値なし
    
    // ページ一覧
    // 初期値なし
    
    // ページ
    // 初期値なし

    /**
     * プラグイン有効時の処理
     *
     * @param $meta
     * @param ContainerInterface $container
     */
    public function enable(array $meta, ContainerInterface $container)
    {
        $em = $container->get('doctrine.orm.entity_manager');
        $em->getConnection()->beginTransaction();
        try {
            $flg = $this->createConfig($container);
            $this->checkWarning($container);
            if (!$flg){
                $this->initStoreType($em);
                $this->initPriceType($em);
                $this->initTaxType($em);
            }
            $em->getConnection()->commit();
        } catch(Exception $e) {
            $em->getConnection()->rollback();
        }
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $meta
     * @param ContainerInterface $container
     */
    public function disable(array $meta, ContainerInterface $container)
    {
    }


     /**
     * 設定情報を入れる
     * @param ContainerInterface $container
     */
    private function createConfig(ContainerInterface $container)
    {
        $em = $container->get('doctrine.orm.entity_manager');
        $Config = $em->find(Config::class, 1);
        if(!$Config){

            $Config = new Config();
            $Config->setName("デフォルト設定");
            
            $em->persist($Config);
            $em->flush($Config);
            
            return false;

        } else{

            return true; // すでにINSERTされてたら後続処理を行わない

        }
    }

    /**
     * 販売店舗スマスタ
     *
     * @param EntityManagerInterface $em
     */
    protected function initStoreType(EntityManagerInterface $em)
    {

        $SmartRegiStore = new SmartRegiStore();
        $SmartRegiStore->setId('1');
        $SmartRegiStore->setName('EC/スマレジ');
        $SmartRegiStore->setSortNo('1');
        $em->persist($SmartRegiStore);
        $em->flush();

        $SmartRegiStore = new SmartRegiStore();
        $SmartRegiStore->setId('2');
        $SmartRegiStore->setName('ECのみ');
        $SmartRegiStore->setSortNo('2');
        $em->persist($SmartRegiStore);
        $em->flush();

        // $SmartRegiStore = new SmartRegiStore();
        // $SmartRegiStore->setId('3');
        // $SmartRegiStore->setName('スマレジのみ');
        // $SmartRegiStore->setSortNo('3');
        // $em->persist($SmartRegiStore);
        // $em->flush();
    }

    /**
     * オープン価格スマスタ
     *
     * @param EntityManagerInterface $em
     */
    protected function initPriceType(EntityManagerInterface $em)
    {

        $SmartRegiPrice = new SmartRegiPrice();
        $SmartRegiPrice->setId('1');
        $SmartRegiPrice->setName('通常価格');
        $SmartRegiPrice->setSortNo('1');
        $em->persist($SmartRegiPrice);
        $em->flush();

        $SmartRegiPrice = new SmartRegiPrice();
        $SmartRegiPrice->setId('2');
        $SmartRegiPrice->setName('オープン価格');
        $SmartRegiPrice->setSortNo('2');
        $em->persist($SmartRegiPrice);
        $em->flush();
    }

    /**
     * 軽減税率対応スマスタ
     *
     * @param EntityManagerInterface $em
     */
    protected function initTaxType(EntityManagerInterface $em)
    {

        $SmartRegiTax = new SmartRegiTax();
        $SmartRegiTax->setId('1');
        $SmartRegiTax->setName('軽減(特定商品の軽減税率適用）');
        $SmartRegiTax->setSortNo('1');
        $em->persist($SmartRegiTax);
        $em->flush();

        $SmartRegiTax = new SmartRegiTax();
        $SmartRegiTax->setId('2');
        $SmartRegiTax->setName('選択[標準] (状態による適用[適用しない])');
        $SmartRegiTax->setSortNo('2');
        $em->persist($SmartRegiTax);
        $em->flush();

        $SmartRegiTax = new SmartRegiTax();
        $SmartRegiTax->setId('3');
        $SmartRegiTax->setName('選択[軽減] (状態による適用[適用する])');
        $SmartRegiTax->setSortNo('3');
        $em->persist($SmartRegiTax);
        $em->flush();

        $SmartRegiTax = new SmartRegiTax();
        $SmartRegiTax->setId('4');
        $SmartRegiTax->setName('選択[選択](状態による適用[都度選択する])');
        $SmartRegiTax->setSortNo('4');
        $em->persist($SmartRegiTax);
        $em->flush();
    }

    public function checkWarning($container){

        // Check if config complete

        $flashbag = $container->get('session')->getFlashBag();
        $Config = $container->get('doctrine.orm.entity_manager')->find(Config::class,1);

        if($Config) {
            if(!$Config->checkConfigComplete()){
                $flashbag->add('eccube.'.'admin'.'.error', 'plg.smartec3.config.not_complete');
            }
        }else{
            $flashbag->add('eccube.'.'admin'.'.error', 'plg.smartec3.config.not_complete');
        }

    }

}

<?php namespace Lovata\OrdersShopaholic\Updates;

use Lovata\Ordersshopaholic\Models\UserAddress;
use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateUserAddressesChangePostcode
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateUserAddressesChangePostcode extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_user_addresses';

    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'postcode')) {
            return;
        }

        $arPostCodeList = (array) UserAddress::lists('postcode', 'id');

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->string('postcode_temp')->after('address2')->nullable();
        });

        foreach ($arPostCodeList as $iAddressID => $iPostcode) {
            UserAddress::where('id', $iAddressID)->update(['postcode_temp' => $iPostcode]);
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn('postcode');
        });

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->renameColumn('postcode_temp', 'postcode');
        });
    }

    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'postcode')) {
            return;
        }

        $arPostCodeList = (array) UserAddress::lists('postcode', 'id');

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->integer('postcode_temp')->after('address2')->nullable();
        });

        foreach ($arPostCodeList as $iAddressID => $iPostcode) {
            UserAddress::where('id', $iAddressID)->update(['postcode_temp' => (int) $iPostcode]);
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn('postcode');
        });

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->renameColumn('postcode_temp', 'postcode');
        });
    }
}

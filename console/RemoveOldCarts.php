<?php namespace Lovata\OrdersShopaholic\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Lovata\OrdersShopaholic\Models\Cart;

/**
 * Class RemoveOldCarts
 * @package Lovata\OrdersShopaholic\Console
 */
class RemoveOldCarts extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'shopaholic:remove-old-cart';

    /**
     * @var string The console command description.
     */
    protected $description = 'Remove old shopping carts';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        //TODO: Доделать удаление и элемнтов корзины
        $obOldCartIds = Cart::where('updated_at', '<',Carbon::now()->subMonth())->lists('id');
        Cart::destroy($obOldCartIds);
        
        $this->output->writeln('Well done!');
    }
}
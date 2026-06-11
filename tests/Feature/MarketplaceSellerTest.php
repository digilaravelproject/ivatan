<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MarketplaceSellerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $sellerA;
    protected User $sellerB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sellerA = User::factory()->create([
            'name' => 'Seller A',
            'username' => 'sellera',
            'is_seller' => true,
        ]);

        $this->sellerB = User::factory()->create([
            'name' => 'Seller B',
            'username' => 'sellerb',
            'is_seller' => true,
        ]);
    }

    /** @test */
    public function it_can_fetch_only_approved_products_of_a_specific_seller()
    {
        // Approved product for Seller A
        $approvedProduct = UserProduct::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Approved Product',
            'slug' => 'seller-a-approved-product',
            'description' => 'Approved product description',
            'price' => 99.99,
            'stock' => 10,
            'status' => 'approved',
        ]);

        // Active product for Seller A
        $activeProduct = UserProduct::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Active Product',
            'slug' => 'seller-a-active-product',
            'description' => 'Active product description',
            'price' => 149.99,
            'stock' => 5,
            'status' => 'active',
        ]);

        // Pending product for Seller A (should be excluded)
        $pendingProduct = UserProduct::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Pending Product',
            'slug' => 'seller-a-pending-product',
            'description' => 'Pending product description',
            'price' => 49.99,
            'stock' => 20,
            'status' => 'pending',
        ]);

        // Rejected product for Seller A (should be excluded)
        $rejectedProduct = UserProduct::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Rejected Product',
            'slug' => 'seller-a-rejected-product',
            'description' => 'Rejected product description',
            'price' => 19.99,
            'stock' => 0,
            'status' => 'rejected',
        ]);

        // Approved product for Seller B (should be excluded as it belongs to Seller B)
        $sellerBProduct = UserProduct::create([
            'seller_id' => $this->sellerB->id,
            'title' => 'Seller B Approved Product',
            'slug' => 'seller-b-approved-product',
            'description' => 'Seller B product description',
            'price' => 299.99,
            'stock' => 15,
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/v1/marketplace/product/{$this->sellerA->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'uuid',
                            'seller_id',
                            'title',
                            'slug',
                            'price',
                            'stock',
                            'status',
                            'images',
                            'seller'
                        ]
                    ],
                    'total'
                ]
            ]);

        $items = $response->json('data.data');

        // Verify that only Seller A's approved and active products are returned
        $this->assertCount(2, $items);
        $productIds = collect($items)->pluck('id')->toArray();

        $this->assertContains($approvedProduct->id, $productIds);
        $this->assertContains($activeProduct->id, $productIds);
        $this->assertNotContains($pendingProduct->id, $productIds);
        $this->assertNotContains($rejectedProduct->id, $productIds);
        $this->assertNotContains($sellerBProduct->id, $productIds);
    }

    /** @test */
    public function it_can_fetch_only_approved_services_of_a_specific_seller()
    {
        // Approved service for Seller A
        $approvedService = UserService::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Approved Service',
            'slug' => 'seller-a-approved-service',
            'description' => 'Approved service description',
            'price' => 50.00,
            'status' => 'approved',
        ]);

        // Active service for Seller A
        $activeService = UserService::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Active Service',
            'slug' => 'seller-a-active-service',
            'description' => 'Active service description',
            'price' => 75.00,
            'status' => 'active',
        ]);

        // Pending service for Seller A (should be excluded)
        $pendingService = UserService::create([
            'seller_id' => $this->sellerA->id,
            'title' => 'Seller A Pending Service',
            'slug' => 'seller-a-pending-service',
            'description' => 'Pending service description',
            'price' => 30.00,
            'status' => 'pending',
        ]);

        // Approved service for Seller B (should be excluded)
        $sellerBService = UserService::create([
            'seller_id' => $this->sellerB->id,
            'title' => 'Seller B Approved Service',
            'slug' => 'seller-b-approved-service',
            'description' => 'Seller B service description',
            'price' => 150.00,
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/v1/marketplace/service/{$this->sellerA->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'uuid',
                            'seller_id',
                            'title',
                            'slug',
                            'price',
                            'status',
                            'images',
                            'seller'
                        ]
                    ],
                    'total'
                ]
            ]);

        $items = $response->json('data.data');

        // Verify that only Seller A's approved and active services are returned
        $this->assertCount(2, $items);
        $serviceIds = collect($items)->pluck('id')->toArray();

        $this->assertContains($approvedService->id, $serviceIds);
        $this->assertContains($activeService->id, $serviceIds);
        $this->assertNotContains($pendingService->id, $serviceIds);
        $this->assertNotContains($sellerBService->id, $serviceIds);
    }
}

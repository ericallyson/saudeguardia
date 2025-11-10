<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

class AddSubscriptionColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedBigInteger('subscription_customer_id')->nullable()->after('whatsapp_instance_uuid');
            $table->unsignedBigInteger('subscription_id')->nullable()->after('subscription_customer_id');
            $table->unsignedBigInteger('subscription_plan_id')->nullable()->after('subscription_id');
            $table->string('subscription_plan_name')->nullable()->after('subscription_plan_id');
            $table->string('subscription_plan_slug')->nullable()->after('subscription_plan_name');
            $table->string('subscription_status')->nullable()->after('subscription_plan_slug');
            $table->date('subscription_trial_ends_at')->nullable()->after('subscription_status');
            $table->date('subscription_next_renewal_date')->nullable()->after('subscription_trial_ends_at');
            $table->decimal('subscription_price', 10, 2)->nullable()->after('subscription_next_renewal_date');
            $table->json('subscription_metadata')->nullable()->after('subscription_price');
            $table->timestamp('subscription_last_synced_at')->nullable()->after('subscription_metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'subscription_customer_id',
                'subscription_id',
                'subscription_plan_id',
                'subscription_plan_name',
                'subscription_plan_slug',
                'subscription_status',
                'subscription_trial_ends_at',
                'subscription_next_renewal_date',
                'subscription_price',
                'subscription_metadata',
                'subscription_last_synced_at',
            ]);
        });
    }
}

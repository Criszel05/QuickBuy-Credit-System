
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ASSET PROCUREMENT AND INTAKE
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('asset_name');
            $table->string('asset_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('supplier')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->default(0);
            $table->string('location')->nullable();
            $table->string('condition')->default('Good');
            $table->string('status')->default('Available');
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. ASSET ASSIGNMENT AND CHECK-OUT
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()->restrictOnDelete();
            $table->foreignId('employee_id')
                ->constrained()->restrictOnDelete();
            $table->foreignId('assigned_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->date('assigned_date');
            $table->date('return_date')->nullable();
            $table->string('condition_on_release')->nullable();
            $table->string('condition_on_return')->nullable();
            $table->string('status')->default('Assigned');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 3. ASSET MOVEMENT AND TRANSFER
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()->restrictOnDelete();
            $table->foreignId('from_employee_id')
                ->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('to_employee_id')
                ->nullable()->constrained('employees')->nullOnDelete();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->foreignId('transferred_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->date('transfer_date');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // 4. MAINTENANCE AND REPAIR
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('maintenance_number')->unique();
            $table->foreignId('asset_id')
                ->constrained()->restrictOnDelete();
            $table->foreignId('reported_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->string('problem_description');
            $table->string('maintenance_type')->default('Repair');
            $table->string('status')->default('Reported');
            $table->string('technician')->nullable();
            $table->date('reported_date');
            $table->date('completed_date')->nullable();
            $table->decimal('repair_cost', 12, 2)->default(0);
            $table->text('repair_notes')->nullable();
            $table->timestamps();
        });

        // 5. SOFTWARE LICENSE MANAGEMENT
        Schema::create('software_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('software_name');
            $table->string('vendor')->nullable();
            $table->string('license_key')->nullable();
            $table->string('license_type')->default('Single User');
            $table->integer('number_of_licenses')->default(1);
            $table->decimal('purchase_cost', 12, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('status')->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // SOFTWARE INSTALLATIONS
        Schema::create('software_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_license_id')
                ->constrained('software_licenses')->restrictOnDelete();
            $table->foreignId('asset_id')
                ->constrained()->restrictOnDelete();
            $table->date('installation_date');
            $table->date('uninstallation_date')->nullable();
            $table->string('status')->default('Installed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. ASSET RETIREMENT AND DISPOSAL
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->string('disposal_method')->nullable();
            $table->string('reason');
            $table->date('retirement_date');
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 12, 2)->default(0);
            $table->string('status')->default('Retired');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
        Schema::dropIfExists('software_installations');
        Schema::dropIfExists('software_licenses');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('asset_transfers');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
    }
};

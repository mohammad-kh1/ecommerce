<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = "full";
    protected static ?int $sort = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort("created_at" , "desc")
            ->columns([
                Tables\Columns\TextColumn::make("id")->label("Order ID")->searchable(),
                Tables\Columns\TextColumn::make("user.name")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("grand_total")->money("USD"),
                Tables\Columns\TextColumn::make("status")->badge()->color(fn($state):string =>match($state){
                    "new" => "info",
                    "processing" => "warning",
                    "shipped" => "success",
                    "delivered" => "success",
                    "cancelled" => "danger"
                })->icon(fn($state):string =>match($state){
                    "new" => "heroicon-m-sparkles",
                    "processing" => "heroicon-m-arrow-path",
                    "shipped" => "heroicon-m-truck",
                    "delivered" => "heroicon-m-check-badge",
                    "cancelled" => "heroicon-m-x-circle"
                })->sortable()->searchable(),

                Tables\Columns\TextColumn::make("payment_method")->sortable()->searchable(),
                Tables\Columns\TextColumn::make("payment_status")->sortable()->searchable()->badge(),

            ])->actions([
                Tables\Actions\Action::make("view order")->url(fn(Order $record):string => OrderResource::getUrl("view" ,["record"=>$record]))
                ->icon("heroicon-m-eye"),
            ]);
    }
}

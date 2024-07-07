<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
//
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make("id")->label("Order ID")->searchable(),
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make("View Order")->url(fn(Order $order):string => OrderResource::getUrl("view" , ["record" => $order]))->color("info")
            ->icon("heroicon-o-eye"),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Card;
use App\Models\Information;
use Illuminate\Http\Request;
use App\Rules\UniqueValueForCard;

class InformationController extends Controller
{
    /**
     * Validate the request data for storing or updating information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $isUpdate
     * @return array
     */
    protected function validateRequest(Request $request, $isUpdate = false)
    {
        // Define validation rules
        $rules = [
            'title' => 'nullable|string|max:30',
            'extra' => 'nullable|string|max:30',
            'card_id' => 'required|int|exists:cards,id',
            'app_id' => 'required|int|exists:apps,id',
            'value' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    // Custom validation logic for 'value'
                    $app = App::find($request->input('app_id'));
                    if (!$app) {
                        return $fail(__('notifications.invalid_app'));
                    }
                    if (!preg_match('/' . $app->regex . '/', $value)) {
                        return $fail(__('notifications.invalid_data_structure'));
                    }
                },
            ],
        ];

        // Add specific rules for update operation
        if ($isUpdate) {
            $rules['id'] = 'required|int|exists:informations,id';
            $rules['value'][] = new UniqueValueForCard(
                $request->input('card_id'),
                $request->input('app_id'),
                $request->input('id')
            );
        } else {
            // Add specific rules for store operation
            $rules['value'][] = new UniqueValueForCard(
                $request->input('card_id'),
                $request->input('app_id')
            );
        }

        // Validate the request data using the defined rules
        return $request->validate($rules);
    }


    /**
     * Store a newly created information in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request);

        $card = Card::findOrFail($request->card_id);
        $this->authorize('owner', $card);

        $app = App::find($request->input('app_id'));
        $order = $app ? $app->default_order : null;
        $validatedData['order'] = $order;
        Information::create($validatedData);

        return redirect()->back()->with('success', __('notifications.information_created'));
    }


    /**
     * Update the specified information in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validatedData = $this->validateRequest($request, true);

        $information = Information::findOrFail($request->id);
        $this->authorize('owner', $information);

        $information->fill($validatedData)->save();

        return redirect()->back()->with('success', __('notifications.information_updated'));
    }


    /**
     * Remove the specified information item from storage.
     *
     * @param  \App\Models\Information  $information
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Information $information)
    {
        $this->authorize('owner', $information);
        $information->delete();

        return redirect()->back()->with('success', __('notifications.information_deleted'));
    }


    /**
     * Update the order of information items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            $information = Information::findOrFail($item['information_id']);

            $this->authorize('owner', $information);

            $information->order = $item['order'];
            $information->save();
        }

        return redirect()->back()->with('success', __('notifications.information_updated'));
    }
}

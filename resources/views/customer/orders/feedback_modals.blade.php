<div x-data="{ open: false, orderId: null, orderNum: '' }" 
     @open-feedback-modal.window="open = true; orderId = $event.detail.orderId; orderNum = $event.detail.orderNum;"
     x-show="open" 
     class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50"
     x-cloak>
    
    <div @click.away="open = false" class="bg-white p-6 rounded-lg w-full max-w-sm shadow-xl transform transition-all duration-300 ease-in-out">
        <h3 class="text-xl font-bold mb-4">Feedback Pesanan #<span x-text="orderNum"></span></h3>
        
        <form :action="`/order/${orderId}/feedback`" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Rating (1-5)</label>
                <input type="number" name="rating" min="1" max="5" required value="5" class="mt-1 block w-20 rounded-md border-gray-300 shadow-sm">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Komentar (Opsional)</label>
                <textarea name="komentar" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" @click="open = false" class="bg-gray-300 py-2 px-4 rounded">Batal</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">Kirim Feedback</button>
            </div>
        </form>
    </div>
</div>
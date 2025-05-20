<form method="POST" action="{{ route('checkout.cache') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="test_image" required>
    <button type="submit">Test Upload</button>
</form>
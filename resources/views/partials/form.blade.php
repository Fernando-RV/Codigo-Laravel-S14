@csrf

<tr>
    <td colspan="2">
        <div class="custom-file">
            <input type="file" name="image" class="custom-file-input" id="customFile">
            <label class="custom-file-label" for="customFile">Seleccione archivo</label>
        </div>
    </td>
</tr>

<tr>
    <th>Categoria</th>
    <td>
        <select name="category_id" id="category_id">
        <option value="">Seleccione</option>
            @foreach($categories as $id => $name)
                <option value="{{ $id }}"
                @if($id == $servicio->category_id) selected @endif
                >{{ $name }}</option>
            @endforeach
        </select>
    </td>
</tr>

<tr>
    <th>Título</th>
    <td><input type="text" name="titulo" value="{{ old('titulo',$servicio->titulo) }}"></td>
</tr>
<tr>
    <th>Descripción</th>
    <td><input type="text" name="description" value="{{ old('description',$servicio->description) }}"></td>
</tr>
<tr>
    <td colspan="2" align="center"><button>{{ $btnText }}</button></td>
</tr>
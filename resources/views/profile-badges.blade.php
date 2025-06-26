<x-layout>
    <x-slot:heading>
        My Badges
    </x-slot>
    <h1>My Badges</h1>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($badges as $badge)
                <tr>
                    <td>{{ $badge->badge_id }}</td>
                    <td>
                        <form method="POST" action="{{ route('profile.badges.rename', $badge->id) }}">
                            @csrf
                            <input type="text" name="name" value="{{ $badge->name }}" autocomplete="off"/>
                            <button type="submit">Rename</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('profile.badges.delete', $badge->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this badge?')">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($badges) < $maxBadges)
        <form method="POST" action="{{ route('profile.badges.add') }}">
            @csrf
            <input type="text" name="name" placeholder="New badge name" required />
            <button type="submit">Add Badge</button>
        </form>
    @else
        @php $isDemo = Auth::user()->demo ?? false; @endphp
        @if ($isDemo)
            <p>You have reached the maximum number of badges allowed for demo accounts ({{ $maxBadges }}). To add more badges, please register for a full account.</p>
        @else
            <p>You have reached the maximum number of badges ({{ $maxBadges }}).</p>
        @endif
    @endif

    <a href="{{ route('profile') }}">&larr; Back to Profile</a>
</x-layout>

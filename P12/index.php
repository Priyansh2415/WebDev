htmlspecialchars($_GET['msg']) . "</p>"; ?>
    <?php if ($error) echo "<p class='err'>" . htmlspecialchars($error) . "</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required>
        <input type="date" name="event_date" required>
        <input type="text" name="location" placeholder="Location" required>
        <select name="category" required>
            <option value="">Category</option>
            <option>Music</option>
            <option>Conference</option>
            <option>Workshop</option>
            <option>Sports</option>
        </select>
        <input type="file" name="poster">
        <button type="submit" name="add">Add Event</button>
    </form>

    <h2>Events</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search title or location" value="<?php echo htmlspecialchars($search); ?>">
        <select name="category">
            <option value="">All categories</option>
            <?php foreach (['Music', 'Conference', 'Workshop', 'Sports'] as $c): ?>
                <option <?php if ($cat == $c) echo 'selected'; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
        <a href="index.php">Reset</a>
    </form>

    <table>
        <tr><th>ID</th><th>Poster</th><th>Title</th><th>Date</th><th>Location</th><th>Category</th><th>Action</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['poster'] ? "<img src='uploads/" . htmlspecialchars($row['poster']) . "'>" : "No poster"; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="index.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this event?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>


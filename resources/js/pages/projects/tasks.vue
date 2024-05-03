<template>
    <v-container>
        <h2>Task List</h2>
        <v-row>
            <v-col v-for="(column, columnIndex) in columns" :key="columnIndex" cols="4">
                <v-card class="mb-3">
                    <v-card-title class="text-center">{{ column.title }}</v-card-title>
                    <v-divider></v-divider>
                    <v-list droppable @drop="onDrop(columnIndex)">
                        <v-list-item v-for="(task, taskIndex) in column.tasks" :key="taskIndex"
                            @click="editTask(columnIndex, taskIndex)" draggable
                            @dragstart="onDragStart(columnIndex, taskIndex)"
                            @dragover="onDragOver($event, columnIndex, taskIndex)" @dragend="onDragEnd">
                            <v-list-item-content>
                                <v-list-item-title>{{ task.title }}</v-list-item-title>
                                <v-list-item-subtitle>{{ task.description }}</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                    <v-divider></v-divider>
                    <v-card-actions class="justify-center">
                        <v-btn @click="addTask(columnIndex)" color="primary">Add Task</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-dialog v-model="dialog" max-width="500">
            <v-card>
                <v-card-title>Edit Task</v-card-title>
                <v-card-text>
                    <v-text-field v-model="editedTask.title" label="Title"></v-text-field>
                    <v-text-field v-model="editedTask.description" label="Description"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" @click="saveTask">Save</v-btn>
                    <v-btn color="error" @click="cancelEdit">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref } from 'vue';

// Define the columns array with initial tasks
const columns = ref([
    { title: 'To Do', tasks: [{ id: 1, title: 'Task 1', description: 'Description of Task 1' }, { id: 2, title: 'Task 2', description: 'Description of Task 2' }] },
    { title: 'In Progress', tasks: [{ id: 3, title: 'Task 3', description: 'Description of Task 3' }] },
    { title: 'Done', tasks: [] }
]);

// Define the dialog state for editing tasks
const dialog = ref(false);

// Define the editedTask object for editing
const editedTask = ref({});

// Method to edit a task
const editTask = (columnIndex, taskIndex) => {
    // Copy the task details to editedTask
    editedTask.value = { columnIndex, taskIndex, ...columns.value[columnIndex].tasks[taskIndex] };
    // Open the dialog
    dialog.value = true;
};

// Method to save the edited task
const saveTask = () => {
    // Extract column index and task index from editedTask
    const { columnIndex, taskIndex } = editedTask.value;
    // Update the task in the columns array
    columns.value[columnIndex].tasks.splice(taskIndex, 1, editedTask.value);
    // Close the dialog
    dialog.value = false;
};

// Method to cancel editing a task
const cancelEdit = () => {
    // Close the dialog
    dialog.value = false;
};

// Method to add a new task
const addTask = (columnIndex) => {
    // Generate a unique ID for the new task
    const id = Date.now();
    // Create a new task object
    const newTask = { id, title: 'New Task', description: 'Description of New Task' };
    // Add the new task to the specified column
    columns.value[columnIndex].tasks.push(newTask);
};

// Method to handle drag start event
const onDragStart = (sourceColumnIndex, sourceTaskIndex) => {
    // Store the dragged task details
    draggedTask.value = { columnIndex: sourceColumnIndex, taskIndex: sourceTaskIndex };
};

// Method to handle drag over event
const onDragOver = (event, targetColumnIndex, targetTaskIndex) => {
    // Prevent default behavior
    event.preventDefault();
    // Store the target position for dropping
    targetPosition.value = { columnIndex: targetColumnIndex, taskIndex: targetTaskIndex };
};

// Method to handle drop event
const onDrop = (targetColumnIndex) => {
    // Retrieve the task being dragged
    const { columnIndex: sourceColumnIndex, taskIndex: sourceTaskIndex } = draggedTask.value;
    // Retrieve the target position for dropping
    const { taskIndex: targetTaskIndex } = targetPosition.value;
    // Move the task to the new column
    const taskToMove = columns.value[sourceColumnIndex].tasks.splice(sourceTaskIndex, 1)[0];
    columns.value[targetColumnIndex].tasks.splice(targetTaskIndex, 0, taskToMove);
};

// Method to handle drag end event
const onDragEnd = () => {
    // Clear the dragged task and target position
    draggedTask.value = null;
    targetPosition.value = null;
};

// Define reactive references for dragged task and target position
const draggedTask = ref(null);
const targetPosition = ref(null);
</script>
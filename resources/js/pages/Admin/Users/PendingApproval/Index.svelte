<script>
    import App from "@/layouts/App.svelte";
    import PendingApprovalCard from "@/components/Admin/Users/PendingApproval/PendingApprovalCard.svelte";
</script>

<App title="OOPedia - Menunggu Persetujuan" showSidebar={false}>
    <div class="max-w-4xl mx-auto py-12">
        <PendingApprovalCard />
    </div>
</App>

<!-- Cancellation Modal -->
<div id="cancellationModal" class="modal" style="display: none;" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
    <div class="modal-overlay" data-dismiss="modal"></div>
    <div class="modal-container">
        <div class="modal-content">
            <!-- Modal Header -->
            <header class="modal-header">
                <h2 id="modalTitle" class="modal-title">Request Order Cancellation</h2>
                <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </header>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="order-info-summary">
                    <p class="text-sm text-gray-600">Order Number: <span id="modalOrderNumber" class="font-semibold text-gray-900"></span></p>
                </div>

                <form id="cancellationForm">
                    <input type="hidden" id="cancelOrderId" name="order_id">
                    
                    <div class="form-group">
                        <label class="form-label">
                            Reason for Cancellation <span class="text-red-500">*</span>
                        </label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="reason" value="changed_mind" class="radio-input">
                                <span class="radio-label">Changed my mind</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="found_better_price" class="radio-input">
                                <span class="radio-label">Found a better price elsewhere</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="ordered_by_mistake" class="radio-input">
                                <span class="radio-label">Ordered by mistake</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="delivery_too_long" class="radio-input">
                                <span class="radio-label">Delivery time is too long</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="want_different_product" class="radio-input">
                                <span class="radio-label">Want to order a different product</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="payment_issues" class="radio-input">
                                <span class="radio-label">Payment issues</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="duplicate_order" class="radio-input">
                                <span class="radio-label">Duplicate order</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="reason" value="other" class="radio-input">
                                <span class="radio-label">Other reason</span>
                            </label>
                        </div>
                        <p class="form-error" id="reasonError" style="display: none;">Please select a reason</p>
                    </div>

                    <div class="form-group" id="otherReasonGroup" style="display: none;">
                        <label for="otherReason" class="form-label">
                            Please specify <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="otherReason" 
                            name="other_reason" 
                            class="form-textarea" 
                            rows="3" 
                            placeholder="Please provide more details..."
                            maxlength="500"></textarea>
                        <p class="form-hint">Maximum 500 characters</p>
                        <p class="form-error" id="otherReasonError" style="display: none;">Please provide details</p>
                    </div>

                    <div class="form-group">
                        <label for="additionalComments" class="form-label">
                            Additional Comments <span class="text-gray-400 text-sm">(Optional)</span>
                        </label>
                        <textarea 
                            id="additionalComments" 
                            name="comments" 
                            class="form-textarea" 
                            rows="3" 
                            placeholder="Any other information you'd like to share..."
                            maxlength="1000"></textarea>
                        <p class="form-hint">Maximum 1000 characters</p>
                    </div>

                    <div class="alert alert-info">
                        <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="alert-title">Please Note:</p>
                            <p class="alert-text">Your cancellation request will be reviewed by our admin team. You will be notified once a decision has been made.</p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <footer class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
                <button type="button" id="submitCancellation" class="btn btn-danger">
                    Submit Request
                </button>
            </footer>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="modal" style="display: none;" role="dialog" aria-labelledby="confirmTitle" aria-modal="true">
    <div class="modal-overlay" data-dismiss-confirm="modal"></div>
    <div class="modal-container modal-sm">
        <div class="modal-content">
            <header class="modal-header">
                <h2 id="confirmTitle" class="modal-title">Confirm Cancellation</h2>
            </header>

            <div class="modal-body">
                <div class="confirmation-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <p class="confirmation-text">
                    Are you sure you want to submit this cancellation request?
                </p>
                <p class="confirmation-subtext">
                    This action cannot be undone. The admin will review your request.
                </p>
            </div>

            <footer class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss-confirm="modal">
                    Go Back
                </button>
                <button type="button" id="confirmCancellation" class="btn btn-danger">
                    Yes, Submit Request
                </button>
            </footer>
        </div>
    </div>
</div>
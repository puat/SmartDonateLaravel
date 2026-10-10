// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

/// @title SmartDonate Audit Registry
/// @notice Kontrak hanya mencatat integritas/verifikasi. Kontrak TIDAK menyimpan atau mentransfer Rupiah.
contract SmartDonate {
    address public owner;
    mapping(uint256 => bool) public campaigns;
    mapping(uint256 => bytes32) public evidenceHashes;
    mapping(uint256 => bool) public verifiedMilestones;
    mapping(uint256 => bool) public releasedMilestones;

    event CampaignCreated(uint256 indexed campaignId, uint256 targetRupiah);
    event DonationRecorded(uint256 indexed campaignId, string donationCode, uint256 amountRupiah);
    event MilestoneCreated(uint256 indexed campaignId, uint256 indexed milestoneId, uint8 percentage, uint256 amountRupiah);
    event EvidenceSubmitted(uint256 indexed milestoneId, bytes32 evidenceHash);
    event MilestoneVerified(uint256 indexed milestoneId, address indexed verifier);
    event FundReleaseAuthorized(uint256 indexed milestoneId, uint256 amountRupiah);
    event FundReleased(uint256 indexed milestoneId, uint256 amountRupiah);

    modifier onlyOwner() { require(msg.sender == owner, "Tidak berwenang"); _; }
    constructor() { owner = msg.sender; }

    function createCampaign(uint256 campaignId, uint256 targetRupiah) external onlyOwner { require(!campaigns[campaignId], "Program sudah ada"); campaigns[campaignId] = true; emit CampaignCreated(campaignId, targetRupiah); }
    function recordDonation(uint256 campaignId, string calldata donationCode, uint256 amountRupiah) external onlyOwner { require(campaigns[campaignId], "Program tidak ada"); emit DonationRecorded(campaignId, donationCode, amountRupiah); }
    function createMilestone(uint256 campaignId, uint256 milestoneId, uint8 percentage, uint256 amountRupiah) external onlyOwner { require(campaigns[campaignId] && percentage > 0 && percentage <= 100, "Data tidak valid"); emit MilestoneCreated(campaignId, milestoneId, percentage, amountRupiah); }
    function submitEvidence(uint256 milestoneId, bytes32 evidenceHash) external onlyOwner { evidenceHashes[milestoneId] = evidenceHash; emit EvidenceSubmitted(milestoneId, evidenceHash); }
    function verifyMilestone(uint256 milestoneId) external onlyOwner { require(evidenceHashes[milestoneId] != bytes32(0), "Bukti belum ada"); verifiedMilestones[milestoneId] = true; emit MilestoneVerified(milestoneId, msg.sender); }
    function authorizeRelease(uint256 milestoneId, uint256 amountRupiah) external onlyOwner { require(verifiedMilestones[milestoneId] && !releasedMilestones[milestoneId], "Belum valid/sudah cair"); emit FundReleaseAuthorized(milestoneId, amountRupiah); }
    function releaseFund(uint256 milestoneId, uint256 amountRupiah) external onlyOwner { require(verifiedMilestones[milestoneId] && !releasedMilestones[milestoneId], "Belum valid/sudah cair"); releasedMilestones[milestoneId] = true; emit FundReleased(milestoneId, amountRupiah); }
}

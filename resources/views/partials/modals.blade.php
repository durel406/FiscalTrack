<!-- ============ MODALS ============ -->

<!-- Modal-Contribuable -->

<div class="overlay" id="ov-modalContribuable">
  <div class="modal">
    <div class="modal-head"><h3 id="modalContribuableTitle">Ajouter un contribuable</h3><button class="mini-btn" onclick="closeModal('modalContribuable')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="modal-subtitle">Identification</div>
      <div class="field"><label>Nom du contribuable</label><input id="f-contrib-nom" placeholder="Ex : SARL KOUAM &amp; FILS"></div>
      <div class="field-row">
        <div class="field"><label>NIU</label><input id="f-contrib-niu" placeholder="M0123456789X"></div>
        <div class="field"><label>Mot de passe</label><input id="f-contrib-pass" type="text" placeholder="Ex : Pass2026"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Régime</label><select id="f-contrib-regime"><option>Réel</option><option>Simplifié</option><option>Classe</option><option>IGS Classe</option><option>NON PROFESSIONNEL</option><option>Autres</option></select><input type="text" id="f-contrib-regime-autre" class="autre-field" placeholder="Précisez le régime" style="display:none;"></div>
        <div class="field"><label>Catégorie / classe</label><select id="f-contrib-cat"><option>Petite entreprise</option><option>Moyenne entreprise</option><option>Grande entreprise</option><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>Autres</option></select><input type="text" id="f-contrib-cat-autre" class="autre-field" placeholder="Précisez la catégorie / classe" style="display:none;"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Lieu</label><input id="f-contrib-lieu" placeholder="Ex : Douala"></div>
        <div class="field"><label>Téléphone</label><input id="f-contrib-tel" placeholder="6XX XX XX XX"></div>
      </div>

      <div class="modal-subtitle">Paiements &amp; charges</div>
      <div class="field-row">
        <div class="field"><label>Montant payé (FCFA)</label><input id="f-contrib-montant" type="number" placeholder="0"></div>
        <div class="field"><label>Impôts payé (FCFA)</label><input id="f-contrib-impots" type="number" placeholder="0"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Loyer (FCFA)</label><input id="f-contrib-loyer" type="number" placeholder="0"></div>
        <div class="field"><label>Bail (FCFA)</label><input id="f-contrib-bail" type="number" placeholder="0"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Précompte (FCFA)</label><input id="f-contrib-precompte" type="number" placeholder="0"></div>
        <div class="field"><label>Timbre (FCFA)</label><input id="f-contrib-timbre" type="number" placeholder="0"></div>
      </div>
      <div class="field"><label>Frais de paiement (FCFA)</label><input id="f-contrib-fraispaiement" type="number" placeholder="0"></div>

      <div class="modal-subtitle" id="f-igs-subtitle">IGS — Trimestres &amp; TDL</div>
      <div class="field-row-5">
        <div class="field"><label>T1</label><input id="f-contrib-t1" type="number" placeholder="0"></div>
        <div class="field"><label>T2</label><input id="f-contrib-t2" type="number" placeholder="0"></div>
        <div class="field"><label>T3</label><input id="f-contrib-t3" type="number" placeholder="0"></div>
        <div class="field"><label>T4</label><input id="f-contrib-t4" type="number" placeholder="0"></div>
        <div class="field"><label>TDL</label><input id="f-contrib-tdl" type="number" placeholder="0"></div>
      </div>

      <div class="modal-subtitle">Frais de suivi</div>
      <div class="field-row">
        <div class="field"><label>Payé (FCFA)</label><input id="f-contrib-fspaye" type="number" placeholder="0"></div>
        <div class="field"><label>Non payé (FCFA)</label><input id="f-contrib-fsnonpaye" type="number" placeholder="0"></div>
      </div>

      <div class="modal-subtitle">Avis d'imposition — dates</div>
      <div class="field-row-3">
        <div class="field"><label>IGS</label><input id="f-contrib-aiigs" type="date"></div>
        <div class="field"><label>Bail</label><input id="f-contrib-aibail" type="date"></div>
        <div class="field"><label>Précompte</label><input id="f-contrib-aiprecompte" type="date"></div>
      </div>

      <div class="modal-subtitle">Quittance — dates</div>
      <div class="field-row-3">
        <div class="field"><label>IGS</label><input id="f-contrib-qigs" type="date"></div>
        <div class="field"><label>Bail</label><input id="f-contrib-qbail" type="date"></div>
        <div class="field"><label>Précompte</label><input id="f-contrib-qprecompte" type="date"></div>
      </div>

      <div class="modal-subtitle">ACF — dates</div>
      <div class="field-row-3">
        <div class="field"><label>IGS</label><input id="f-contrib-acfigs" type="date"></div>
        <div class="field"><label>Bail</label><input id="f-contrib-acfbail" type="date"></div>
        <div class="field"><label>Précompte</label><input id="f-contrib-acfprecompte" type="date"></div>
      </div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" id="modalContribuableCancelBtn" onclick="closeModal('modalContribuable')">Annuler</button><button class="btn btn-primary" id="modalContribuableSaveBtn" onclick="submitContribuable()">Enregistrer</button></div>
  </div>
</div>

<!-- Modal-Document -->

<div class="overlay" id="ov-modalDocument">
  <div class="modal">
    <div class="modal-head"><h3 id="modalDocumentTitle">Ajouter un document</h3><button class="mini-btn" onclick="closeModal('modalDocument')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field"><label>Nom du document</label><input id="f-doc-nom" placeholder="Ex : Quittance IGS T3"></div>
      <div class="field-row">
        <div class="field"><label>Type</label><select id="f-doc-type" onchange="onDocTypeChange()"><option>Avis d'imposition</option><option>Quittance</option><option>Reçue</option><option>Facture de vente</option><option>Facture d'achat</option><option>Attestation d'immatriculation</option><option>ATMP</option><option>ACS</option><option>ACF</option><option>Autres</option></select><input type="text" id="f-doc-type-autre" class="autre-field" placeholder="Précisez le type de document" style="display:none;"></div>
        <div class="field"><label>Contribuable associé</label><select id="f-doc-contrib"></select></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Fournisseur</label><input id="f-doc-fournisseur" placeholder="Ex : DGI Cameroun"></div>
        <div class="field"><label id="f-doc-montant-label">Montant (FCFA)</label><input id="f-doc-montant" type="number" placeholder="0"></div>
      </div>
      <div class="field">
        <label>Fichier numérique (PDF, PNG)</label>
        <input type="file" id="f-doc-file">
        <p id="doc-file-hint" style="display:none;font-size:11px;color:var(--text-400);margin-top:5px;">Laissez ce champ vide pour conserver le fichier déjà enregistré.</p>
      </div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" onclick="closeModal('modalDocument')">Annuler</button><button class="btn btn-primary" id="modalDocumentSaveBtn" onclick="submitDocument()">Enregistrer</button></div>
  </div>
</div>

<div class="overlay" id="ov-modalDocumentView">
  <div class="modal">
    <div class="modal-head"><h3>Fiche du document</h3><button class="mini-btn" onclick="closeModal('modalDocumentView')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field"><label>Nom</label><div class="cell-strong" id="fiche-doc-nom">—</div></div>
      <div class="field-row">
        <div class="field"><label>Type</label><div id="fiche-doc-type">—</div></div>
        <div class="field"><label>Contribuable associé</label><div id="fiche-doc-contrib">—</div></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Fournisseur</label><div id="fiche-doc-fournisseur">—</div></div>
        <div class="field"><label>Montant (FCFA)</label><div id="fiche-doc-montant">—</div></div>
      </div>
      <div class="field"><label>Date d'ajout</label><div id="fiche-doc-date">—</div></div>
      <div class="field" id="fiche-doc-preview-wrap" style="display:none;">
        <label>Aperçu du fichier</label>
        <div id="fiche-doc-preview"></div>
      </div>
      <div class="field" id="fiche-doc-nofile" style="display:none;">
        <div class="empty" style="padding:10px 0;">Aucun fichier numérique n'a été associé à ce document.</div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal('modalDocumentView')">Fermer</button>
      <button class="btn btn-primary" id="fiche-doc-download-btn"><svg><use href="#i-download"/></svg>Télécharger</button>
    </div>
  </div>
</div>

<div class="overlay" id="ov-modalUser">
  <div class="modal">
    <div class="modal-head"><h3 id="modalUserTitle">Créer un compte utilisateur</h3><button class="mini-btn" onclick="closeModal('modalUser')"><svg><use href="#i-x"/></svg></button></div>
    <div class="modal-body">
      <div class="field-row">
        <div class="field"><label>Nom</label><input id="f-user-nom" placeholder="Nom"></div>
        <div class="field"><label>Prénom</label><input id="f-user-prenom" placeholder="Prénom"></div>
      </div>
      <div class="field"><label>Email</label><input id="f-user-email" type="email" placeholder="prenom.nom@fiscaltrack.test"></div>
      <div class="field-row">
        <div class="field"><label>Rôle</label><select id="f-user-role"><option>Comptable</option><option>Responsable fiscal</option><option>Administrateur</option><option>Autres</option></select><input type="text" id="f-user-role-autre" class="autre-field" placeholder="Précisez le rôle" style="display:none;"></div>
        <div class="field"><label>Statut</label><select id="f-user-statut"><option>Actif</option><option>Suspendu</option></select></div>
      </div>
      <div class="field"><label>Mot de passe temporaire</label><input id="f-user-pass" type="password" placeholder="••••••••"></div>
    </div>
    <div class="modal-foot"><button class="btn btn-ghost" onclick="closeModal('modalUser')">Annuler</button><button class="btn btn-primary" id="modalUserSaveBtn" onclick="submitUser()">Créer le compte</button></div>
  </div>
</div>

<!-- déconnexion -->
<div class="lock-overlay" id="lockOverlay">
  <div class="lock-card">
    <div class="brand-mark"><svg><use href="#i-logo"/></svg></div>
    <h2>Vous êtes déconnecté</h2>
    <p>Votre session FiscalTrack a été fermée en toute sécurité. Reconnectez-vous pour accéder à votre espace de travail.</p>
    <a class="btn btn-primary" style="justify-content:center;width:100%;text-decoration:none;" href="{{ route('login') }}">Se reconnecter</a>
  </div>
</div>
